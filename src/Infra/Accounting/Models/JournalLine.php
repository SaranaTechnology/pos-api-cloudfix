<?php

namespace Infra\Accounting\Models;

use Infra\Shared\Models\BaseModel;

class JournalLine extends BaseModel
{
    protected $table = 'journal_lines';

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}

