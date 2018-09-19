<?php

declare(strict_types=1);

namespace App\PublicModule\Presenters;

use Nittro\Bridges\NittroUI\Presenter;


abstract class BasePresenter extends Presenter {

    protected $title = 'Nittro project skeleton';

    protected function afterRender() : void {
        parent::afterRender();
        $this->template->title = $this->payload->title = $this->title;
    }

}
