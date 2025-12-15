#!/bin/bash
# =============================================================================
# Deploy Script for Cashier POS API
# Build and push Docker image to Amazon ECR
# =============================================================================

set -e

# Configuration
AWS_REGION="ap-southeast-1"
REPOSITORY_NAME="api-cloudfix-cashier-pos"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Print banner
print_banner() {
    echo -e "${BLUE}"
    echo "╔═══════════════════════════════════════════════════════════╗"
    echo "║        Cashier POS API - ECR Deployment Script            ║"
    echo "╚═══════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

# Parse command line arguments
parse_arguments() {
    CLIENT_NAME=""
    ENVIRONMENT="dev"
    ENV_PREFIX="dev"

    while [[ $# -gt 0 ]]; do
        case $1 in
            --client|-c)
                CLIENT_NAME="$2"
                shift 2
                ;;
            --client=*)
                CLIENT_NAME="${1#*=}"
                shift
                ;;
            --env|-e)
                ENVIRONMENT="$2"
                shift 2
                ;;
            --env=*)
                ENVIRONMENT="${1#*=}"
                shift
                ;;
            --help)
                show_help
                exit 0
                ;;
            *)
                log_error "Unknown option: $1"
                show_help
                exit 1
                ;;
        esac
    done

    if [ -z "$CLIENT_NAME" ]; then
        log_error "Client name is required. Use --client <name>"
        show_help
        exit 1
    fi

    # Set ENV_PREFIX based on environment
    case $ENVIRONMENT in
        dev|development)
            ENV_PREFIX="dev"
            ;;
        staging)
            ENV_PREFIX="staging"
            ;;
        prod|production)
            ENV_PREFIX="prod"
            ;;
        *)
            ENV_PREFIX="dev"
            ;;
    esac
}

show_help() {
    echo ""
    echo "Usage: ./deploy.sh --client <client_name> [--env <environment>]"
    echo ""
    echo "Options:"
    echo "  --client, -c    Client name (required). This will:"
    echo "                  - Load .env.<client> file"
    echo "                  - Tag image as dev.<client>-latest"
    echo "                  - Tag image as dev.<client>.<timestamp>"
    echo "  --env, -e       Environment (dev|staging|prod) [default: dev]"
    echo ""
    echo "Tag Format:"
    echo "  Latest:     {env}.{client}-latest     (e.g., dev.likely-latest)"
    echo "  Versioned:  {env}.{client}.{timestamp} (e.g., dev.likely.20241211_145230)"
    echo ""
    echo "Examples:"
    echo "  ./deploy.sh --client likely"
    echo "  ./deploy.sh --client likely --env staging"
    echo "  ./deploy.sh -c likely -e prod"
    echo ""
}

# Get AWS Account ID
get_aws_account_id() {
    log_info "Getting AWS Account ID..."
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text 2>/dev/null)
    
    if [ -z "$AWS_ACCOUNT_ID" ]; then
        log_error "Failed to get AWS Account ID. Make sure AWS CLI is configured."
        exit 1
    fi
    
    log_success "AWS Account ID: $AWS_ACCOUNT_ID"
}

# Validate AWS credentials
validate_aws_credentials() {
    log_info "Validating AWS credentials..."
    
    if ! aws sts get-caller-identity &>/dev/null; then
        log_error "AWS credentials are not configured or invalid"
        log_info "Please run: aws configure"
        exit 1
    fi
    
    local identity=$(aws sts get-caller-identity --output json)
    local user_arn=$(echo $identity | jq -r '.Arn')
    
    log_success "Authenticated as: $user_arn"
}

# Load environment file (skip AWS credentials)
load_env_file() {
    local env_file=".env.${CLIENT_NAME}"
    
    if [ -f "$env_file" ]; then
        log_info "Loading environment from $env_file (skipping AWS credentials)..."
        
        while IFS='=' read -r key value; do
            # Skip comments and empty lines
            [[ $key =~ ^#.*$ ]] && continue
            [[ -z "$key" ]] && continue
            
            # Skip AWS credentials - we use system credentials instead
            [[ "$key" == "AWS_ACCESS_KEY_ID" ]] && continue
            [[ "$key" == "AWS_SECRET_ACCESS_KEY" ]] && continue
            
            # Remove surrounding quotes from value
            value="${value%\"}"
            value="${value#\"}"
            value="${value%\'}"
            value="${value#\'}"
            
            # Export non-AWS credential variables
            export "$key=$value"
        done < "$env_file"
        
        log_success "Environment loaded from $env_file"
    else
        log_warning "Environment file $env_file not found, using defaults"
    fi
}

# Login to ECR
ecr_login() {
    log_info "Logging in to Amazon ECR..."
    
    ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
    
    aws ecr get-login-password --region "$AWS_REGION" | \
        docker login --username AWS --password-stdin "$ECR_REGISTRY"
    
    log_success "Logged in to ECR: $ECR_REGISTRY"
}

# Create ECR repository if it doesn't exist
ensure_ecr_repository() {
    log_info "Checking ECR repository..."
    
    if ! aws ecr describe-repositories --repository-names "$REPOSITORY_NAME" --region "$AWS_REGION" &>/dev/null; then
        log_info "Creating ECR repository: $REPOSITORY_NAME"
        aws ecr create-repository \
            --repository-name "$REPOSITORY_NAME" \
            --region "$AWS_REGION" \
            --image-scanning-configuration scanOnPush=true \
            --encryption-configuration encryptionType=AES256
        log_success "Repository created"
    else
        log_success "Repository exists: $REPOSITORY_NAME"
    fi
}

# Build Docker image
build_image() {
    log_info "Building Docker image..."

    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    TAG_LATEST="${CLIENT_NAME}-latest"
    TAG_VERSIONED="${CLIENT_NAME}-${TIMESTAMP}"

    FULL_IMAGE_LATEST="${ECR_REGISTRY}/${REPOSITORY_NAME}:${TAG_LATEST}"
    FULL_IMAGE_VERSIONED="${ECR_REGISTRY}/${REPOSITORY_NAME}:${TAG_VERSIONED}"

    log_info "Environment: $ENVIRONMENT"
    log_info "Client: $CLIENT_NAME"
    log_info "Building with tags:"
    log_info "  Latest:     $TAG_LATEST"
    log_info "  Versioned:  $TAG_VERSIONED"

    docker build \
        --platform linux/amd64 \
        --build-arg CLIENT_NAME="$CLIENT_NAME" \
        --build-arg APP_ENV="$ENVIRONMENT" \
        -t "$FULL_IMAGE_LATEST" \
        -t "$FULL_IMAGE_VERSIONED" \
        .

    log_success "Image built successfully"
}

# Push image to ECR
push_image() {
    log_info "Pushing image to ECR..."
    
    docker push "$FULL_IMAGE_LATEST"
    log_success "Pushed: $FULL_IMAGE_LATEST"
    
    docker push "$FULL_IMAGE_VERSIONED"
    log_success "Pushed: $FULL_IMAGE_VERSIONED"
}

# Print summary
print_summary() {
    echo ""
    echo -e "${GREEN}╔═══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║                  Deployment Complete!                     ║${NC}"
    echo -e "${GREEN}╚═══════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}Client:${NC}      $CLIENT_NAME"
    echo -e "${BLUE}Environment:${NC} $ENVIRONMENT"
    echo -e "${BLUE}Registry:${NC}    $ECR_REGISTRY"
    echo -e "${BLUE}Repository:${NC}  $REPOSITORY_NAME"
    echo ""
    echo -e "${BLUE}Tags:${NC}"
    echo -e "  ${GREEN}Latest:${NC}    $TAG_LATEST"
    echo -e "  ${GREEN}Versioned:${NC} $TAG_VERSIONED"
    echo ""
    echo -e "${BLUE}Images pushed:${NC}"
    echo "  - $FULL_IMAGE_LATEST"
    echo "  - $FULL_IMAGE_VERSIONED"
    echo ""
    echo -e "${BLUE}Pull commands:${NC}"
    echo "  docker pull $FULL_IMAGE_LATEST"
    echo "  docker pull $FULL_IMAGE_VERSIONED"
    echo ""
}

# Main execution
main() {
    print_banner
    parse_arguments "$@"
    validate_aws_credentials
    get_aws_account_id
    load_env_file
    ecr_login
    ensure_ecr_repository
    build_image
    push_image
    print_summary
}

main "$@"
