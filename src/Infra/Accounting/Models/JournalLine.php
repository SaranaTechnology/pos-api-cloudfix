<?php

namespace Infra\Accounting\Models;

use Infra\Shared\Models\BaseModel;

class JournalLine extends BaseModel
{

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}

