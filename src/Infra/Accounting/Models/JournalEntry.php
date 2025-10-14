<?php

namespace Infra\Accounting\Models;

use Infra\Shared\Models\BaseModel;

class JournalEntry extends BaseModel
{

    public function lines()
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }
}

