<?php

namespace Infra\Shared\Enums;

enum HttpStatus: int
{
    case Ok = 200;

    case Created = 201;

    case BadRequest = 400;

    case Unauthorized = 401;

    case NotFound = 404;

    case NotAcceptable = 406;

    case UnprocessableEntity = 422;

    case InternalError = 500;
}
