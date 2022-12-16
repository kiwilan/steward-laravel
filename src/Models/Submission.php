<?php

namespace Kiwilan\Steward\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Traits\HasAttachment;
use Kiwilan\Steward\Traits\Mediable;

class Submission extends Model
{
    use HasFactory;
    use HasAttachment;
    use Mediable;

    protected $fillable = [
        'name',
        'email',
        'message',
    ];

    // protected $appends = [
    //     'attachment_file',
    // ];

    // public function getAttachmentFileAttribute()
    // {
    //     return $this->file
    //         ? config('app.url')."/storage{$this->file}"
    //         : null;
    // }
}
