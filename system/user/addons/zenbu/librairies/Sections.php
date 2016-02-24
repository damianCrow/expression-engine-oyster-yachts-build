<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\librairies\platform\ee\SectionBase as SectionBase;

class Sections extends SectionBase
{
    public function __construct()
    {
        parent::__construct();
        $this->section_base = new parent();
    }

    public function getSections()
    {
        return $this->section_base->getSections();
    }

    public function getSubSections($section_id = 0)
    {
        return $this->section_base->getSubSections($section_id);
    }

    /**
     * Retrieve a list of sections for settings select dropown
     * @return array The section array
     */
    public function getSectionSelectOptions()
    {
        $sections         = $this->getSections();
        $dropdown_options = parent::buildSelectOptions($sections);

        return $dropdown_options;

    } // END getSectionSelectOptions

    // --------------------------------------------------------------------
}