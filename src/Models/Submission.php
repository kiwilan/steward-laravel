<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kiwilan\Traits\HasAttachment;
use Kiwilan\Traits\Mediable;

class Submission extends Model
{
    use HasFactory;
    use HasAttachment;
    use Mediable;

    protected $fillable = [
        'name',
        'email',
        'message',
        'file',
    ];

    protected $appends = [
        'attachment_file',
    ];

    public function getAttachmentFileAttribute()
    {
        return $this->file
            ? config('app.url')."/storage{$this->file}"
            : null;
    }
}
