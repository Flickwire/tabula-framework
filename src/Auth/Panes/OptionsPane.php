<?php
namespace Tabula\Auth\Panes;

//use Tabula\Models\Options;
use Tabula\Renderer\Page;
use Tabula\Admin\AdminPane;

class OptionsPane extends AdminPane {
    private $request;
    //private $optionsModel;

    public function render(): string{
        $page = new Page($this->tabula,"admin/panes/auth/options.html");
        $this->request = $this->tabula->registry->getRequest();
        //$this->optionsModel = new Options($tabula->db);

        return $page->render(true);
    }

    /**
     * Return the name of your admin pane,
     * for the menu
     */
    public function getName(): string{
        return "Options";
    }

    /**
     * Return a url-friendly slug for your pane
     */
    public function getSlug(): string{
        return "auth-options";
    }

    /**
     * Return an icon for the menu if you want to
     */
    public function getIcon(): ?string{
        return "cog";
    }
}