<?php

namespace App\Observers;

use App\Helpers\Dixa;
use App\Models\Action;
use App\Models\Document;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        $user = Auth::user();
        $action = Action::where('name', Dixa::ACTION_CREATED)->first();
        $history = new History();
        $history->document()->associate($document);
        $history->user()->associate($user);
        $history->action()->associate($action);
        $history->save();
    }

    /**
     * Handle the Document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        $user = Auth::user();
        $action = Action::where('name', Dixa::ACTION_UPDATED)->first();
        $history = new History();
        $history->document()->associate($document);
        $history->user()->associate($user);
        $history->action()->associate($action);
        $history->save();
    }

    /**
     * Handle the Document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        $user = Auth::user();
        $action = Action::where('name', Dixa::ACTION_DELETED)->first();
        $history = new History();
        $history->document()->associate($document);
        $history->user()->associate($user);
        $history->action()->associate($action);
        $history->save();
    }

    /**
     * Handle the Document "restored" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        //
    }
}
