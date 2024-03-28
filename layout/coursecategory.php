<?php
global $USER, $DB;

if (isloggedin() && !isguestuser()) {
    defined('MOODLE_INTERNAL') || die();

    require_once($CFG->libdir . '/behat/lib.php');
    require_once($CFG->dirroot . '/course/lib.php');

    require_once($CFG->dirroot . '/lib/modinfolib.php');

    $modules = new stdClass();

    try{
        $course = $DB->get_record('course', array('idnumber' => 'CCSE'));
        
        $courseModulesObject = get_fast_modinfo($course->id);
        $courseModules = $courseModulesObject->get_cms();
    
        foreach ($courseModules as $module){
            if (($module->modname == 'forum' || $module->modname == 'data') && $module->deletioninprogress == 0){
                $moduleIdentifier = $module->modname;
                $modules->$moduleIdentifier = $module->id;
            }
        }
    }catch(Exception $e){
        echo $e->getMessage();
    }

    // Add block button in editing mode.
    $addblockbutton = $OUTPUT->addblockbutton();

    //user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
    //user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

    if (defined('BEHAT_SITE_RUNNING')) {
        $blockdraweropen = true;
    }

    $extraclasses = ['uses-drawers'];
    if ($courseindexopen) {
        $extraclasses[] = 'drawer-open-index';
    }

    $blockshtml = $OUTPUT->blocks('side-pre');
    $bodyattributes = $OUTPUT->body_attributes($extraclasses);
    $forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

    $secondarynavigation = false;
    $overflow = '';
    if ($PAGE->has_secondary_navigation()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
        if (!is_null($overflowdata)) {
            $overflow = $overflowdata->export_for_template($OUTPUT);
        }
    }

    $primary = new core\navigation\output\primary($PAGE);
    $renderer = $PAGE->get_renderer('core');
    $primarymenu = $primary->export_for_template($renderer);
    $buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
    // If the settings menu will be included in the header then don't add it here.
    $regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

    $header = $PAGE->activityheader;
    $headercontent = $header->export_for_template($renderer);

    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
        'output' => $OUTPUT,
        'sidepreblocks' => $blockshtml,
        'hasblocks' => $hasblocks,
        'bodyattributes' => $bodyattributes,
        'courseindexopen' => $courseindexopen,
        'blockdraweropen' => $blockdraweropen,
        'courseindex' => $courseindex,
        'primarymoremenu' => $primarymenu['moremenu'],
        'secondarymoremenu' => $secondarynavigation ?: false,
        'mobileprimarynav' => $primarymenu['mobileprimarynav'],
        'usermenu' => $primarymenu['user'],
        'langmenu' => $primarymenu['lang'],
        'cohorts' => $cohorts,
        'forceblockdraweropen' => $forceblockdraweropen,
        'regionmainsettingsmenu' => $regionmainsettingsmenu,
        'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
        'overflow' => $overflow,
        'headercontent' => $headercontent,
        'addblockbutton' => $addblockbutton,
        'contentcategory' => $OUTPUT->main_content(),
        'logofooter' => $OUTPUT->image_url('FOSlogo-footer', 'theme_fos_space7'),
        'modules' => $modules

    ];
    echo $OUTPUT->render_from_template('theme_fos_space7/coursecategory', $templatecontext);
} else {
    redirect(get_login_url());
}