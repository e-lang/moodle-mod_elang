<?php
/**
 * The main elang configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

// Get the moodle form library.
require_once($CFG->dirroot . '/course/moodleform_mod.php');

// Get the local library.
require_once(dirname(__FILE__) . '/locallib.php');

/**
 * Module instance settings form
 *
 * @since  0.0.1
 */
class mod_elang_mod_form extends moodleform_mod
{
    /**
     * constructor
     *
     * @param   mixed  $current  Not documented in moodle
     * @param   mixed  $section  Not documented in moodle
     * @param   mixed  $cm       Not documented in moodle
     * @param   mixed  $course   Not documented in moodle
     *
     * @since  1.3.2
     */
    public function __construct($current, $section, $cm, $course) {
        $mimetypes = & core_filetypes::get_types();

        if (!array_key_exists('srt', $mimetypes)) {
            core_filetypes::add_type('srt', 'text/plain', 'text', array(), false, 'Sub Rip track files');
        }

        if (!array_key_exists('vtt', $mimetypes)) {
            core_filetypes::add_type('vtt', 'text/plain', 'text', array(), false, 'HTML track files');
        }

        parent::__construct($current, $section, $cm, $course);
    }

    /**
     * The \Captioning\Format\WebvttFile object is successfull
     *
     * @var  \Captioning\Format\WebvttFile  The vtt object
     *
     * @since  0.0.1
     */
    protected $vtt;

    /**
     * Get the vtt object
     *
     * @return  \Captioning\Format\WebvttFile  The vtt object
     *
     * @since  0.0.1
     */
    public function get_vtt() {
        return $this->vtt;
    }

    /**
     * Defines forms elements
     *
     * @return  void
     *
     * @since  0.0.1
     */
    public function definition() {
        global $CFG;

        // Get the setting for elang.
        $config = get_config('elang');

        // Get the video maxsize.
        $videomaxsize = $config->videomaxsize;

        if (preg_match('/Gb$/', $videomaxsize)) {
            $videomaxsize = intval(floatval($videomaxsize) * 1000000000);
        } else if (preg_match('/Mb$/', $videomaxsize)) {
            $videomaxsize = intval(floatval($videomaxsize) * 1000000);
        } else if (preg_match('/Kb$/', $videomaxsize)) {
            $videomaxsize = intval(floatval($videomaxsize) * 1000);
        } else {
            $videomaxsize = intval($videomaxsize);
        }

        // Get the subtitle maxsize.
        $subtitlemaxsize = $config->subtitlemaxsize;

        if (preg_match('/Gb$/', $subtitlemaxsize)) {
            $subtitlemaxsize = intval(floatval($subtitlemaxsize) * 1000000000);
        } else if (preg_match('/Mb$/', $subtitlemaxsize)) {
            $subtitlemaxsize = intval(floatval($subtitlemaxsize) * 1000000);
        } else if (preg_match('/Kb$/', $subtitlemaxsize)) {
            $subtitlemaxsize = intval(floatval($subtitlemaxsize) * 1000);
        } else {
            $subtitlemaxsize = intval($subtitlemaxsize);
        }

        // Get the poster maxsize.
        $postermaxsize = $config->postermaxsize;

        if (preg_match('/Gb$/', $postermaxsize)) {
            $postermaxsize = intval(floatval($postermaxsize) * 1000000000);
        } else if (preg_match('/Mb$/', $postermaxsize)) {
            $postermaxsize = intval(floatval($postermaxsize) * 1000000);
        } else if (preg_match('/Kb$/', $postermaxsize)) {
            $postermaxsize = intval(floatval($postermaxsize) * 1000);
        } else {
            $postermaxsize = intval($postermaxsize);
        }

        // Get the form.
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('elangname', 'elang'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'elangname', 'elang');

        // Adding the standard "intro" and "introformat" fields.
        if (version_compare(moodle_major_version(true), '2.9', '>=')) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor($config->requiremodintro);
        }

        // Adding the rest of elang settings, spreeading all them into this fieldset.
        $mform->addElement('header', 'elangfieldset', get_string('upload', 'elang'));

        $languages = Elang\get_languages();
        $options = array();

        foreach (explode(',', $config->language) as $key) {
            $options[$key] = $languages[$key];
        }

        $element = $mform->addElement('select', 'language', get_string('language', 'elang'), $options);

        if ($options['en-GB']) {
            $element->setSelected('en-GB');
        }

        $mform->addRule('language', null, 'required', null, 'client');

        $mform->addElement(
            'filemanager',
            'videos',
            get_string('videos', 'elang'),
            null,
            array('subdirs' => 0, 'maxbytes' => $videomaxsize, 'maxfiles' => 20, 'accepted_types' => array('.webm', '.ogv', '.mp4'))
        );
        $mform->addHelpButton('videos', 'videos', 'elang');
        $mform->addRule('videos', null, 'required', null, 'client');

        $mform->addElement(
            'filemanager',
            'subtitle',
            get_string('subtitle', 'elang'),
            null,
            array('subdirs' => 0, 'maxbytes' => $subtitlemaxsize, 'maxfiles' => 1, 'accepted_types' => array('.vtt', '.srt'))
        );
        $mform->addHelpButton('subtitle', 'subtitle', 'elang');
        $mform->addRule('subtitle', null, 'required', null, 'client');

        $mform->addElement(
            'filemanager',
            'poster',
            get_string('poster', 'elang'),
            null,
            array('subdirs' => 0, 'maxbytes' => $postermaxsize, 'maxfiles' => 1, 'accepted_types' => array('image'))
        );
        $mform->addHelpButton('poster', 'poster', 'elang');

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'setting', get_string('settings', 'elang'));

        $mform->addElement('checkbox', 'showlanguage', get_string('showlanguage', 'elang'));
        $mform->addHelpButton('showlanguage', 'showlanguage', 'elang');

        $mform->addElement('text', 'repeatedunderscore', get_string('repeatedunderscore', 'elang'));
        $mform->addHelpButton('repeatedunderscore', 'repeatedunderscore', 'elang');
        $mform->addRule('repeatedunderscore', get_string('repeatedunderscore_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('repeatedunderscore', PARAM_INT);

        $mform->addElement('text', 'titlelength', get_string('titlelength', 'elang'));
        $mform->addHelpButton('titlelength', 'titlelength', 'elang');
        $mform->addRule('titlelength', get_string('titlelength_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('titlelength', PARAM_INT);

        $element = $mform->addElement(
            'select',
            'limit',
            get_string('limit', 'elang'),
            array(5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25)
        );
        $mform->addHelpButton('limit', 'limit', 'elang');
        $mform->addRule('limit', get_string('limit_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('limit', PARAM_INT);
        $element->setSelected(10);

        $mform->addElement('text', 'left', get_string('left', 'elang'));
        $mform->addHelpButton('left', 'left', 'elang');
        $mform->addRule('left', get_string('left_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('left', PARAM_INT);

        $mform->addElement('text', 'top', get_string('top', 'elang'));
        $mform->addHelpButton('top', 'top', 'elang');
        $mform->addRule('top', get_string('top_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('top', PARAM_INT);

        $mform->addElement('text', 'size', get_string('size', 'elang'));
        $mform->addHelpButton('size', 'size', 'elang');
        $mform->addRule('size', get_string('size_error', 'elang'), 'numeric', null, 'client');
        $mform->setType('size', PARAM_INT);

        $mform->addElement('checkbox', 'usecasesensitive', get_string('usecasesensitive', 'elang'));
        $mform->addHelpButton('usecasesensitive', 'usecasesensitive', 'elang');

        $mform->addElement('checkbox', 'usetransliteration', get_string('usetransliteration', 'elang'));
        $mform->addHelpButton('usetransliteration', 'usetransliteration', 'elang');

        $mform->addElement('text', 'jaroDistance', get_string('jaroDistance', 'elang'));
        $mform->setType('jaroDistance', PARAM_FLOAT);
        $mform->addHelpButton('jaroDistance', 'jaroDistance', 'elang');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

        // Add a warning when a module is being updated.
        if (!empty($this->current->instance)) {
            $mform->addElement('warning', null, null, get_string('update_warning', 'elang'));
        }
    }

    /**
     * Add completion rules
     *
     * @return  array  Array of string IDs of added items, empty array if none
     *
     * @since  1.2.0
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $group = array();
        $group[] = $mform->createElement(
            'checkbox',
            'completion_gapfilledenabled',
            '',
            get_string('completion:gapfilled', 'elang')
        );
        $group[] = $mform->createElement('text', 'completion_gapfilled', '', array('size' => 3));
        $mform->setType('completion_gapfilled', PARAM_INT);
        $mform->addGroup($group, 'completion_gapfilled_group', get_string('completion:gapfilledgroup', 'elang'), array(' '), false);
        $mform->disabledIf('completion_gapfilled', 'completion_gapfilledenabled', 'notchecked');

        $group = array();
        $group[] = $mform->createElement(
            'checkbox',
            'completion_gapcompletedenabled',
            '',
            get_string('completion:gapcompleted', 'elang')
        );
        $group[] = $mform->createElement('text', 'completion_gapcompleted', '', array('size' => 3));
        $mform->setType('completion_gapcompleted', PARAM_INT);
        $mform->addGroup(
            $group,
            'completion_gapcompleted_group',
            get_string('completion:gapcompletedgroup', 'elang'),
            array(' '),
            false
        );
        $mform->disabledIf('completion_gapcompleted', 'completion_gapcompletedenabled', 'notchecked');

        return array('completion_gapcompleted_group', 'completion_gapfilled_group');
    }

    /**
     * Called during validation. Override to indicate, based on the data, whether
     * a custom completion rule is enabled (selected).
     *
     * @param   array  $data  Input data (not yet validated)
     *
     * @return  boolean  true if one or more rules is enabled, false if none are; default returns false
     *
     * @since  1.2.0
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completion_gapfilledenabled'])
            && $data['completion_gapfilled'] > 0
            && $data['completion_gapfilled'] <= 100 || !empty($data['completion_gapcompletedenabled'])
            && $data['completion_gapcompleted'] > 0
            && $data['completion_gapcompleted'] <= 100;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return  object  submitted data; NULL if not valid or not submitted or cancelled
     *
     * @since  1.2.0
     */
    public function get_data() {
        $data = parent::get_data();

        if (!$data) {
            return false;
        }

        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;

            if (empty($data->completion_gapfilledenabled) || !$autocompletion) {
                $data->completion_gapfilled = 0;
            }

            if (empty($data->completion_gapcompletedenabled) || !$autocompletion) {
                $data->completion_gapcompleted = 0;
            }
        }

        return $data;
    }

    /**
     * Preprocess data before creating form
     *
     * @param   array  $defaultvalues  Array of default values
     *
     * @return  void
     *
     * @since  0.0.1
     */
    public function data_preprocessing(&$defaultvalues) {
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('videos');
            file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_elang',
                'videos',
                0
            );
            $defaultvalues['videos'] = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('subtitle');
            file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_elang',
                'subtitle',
                0
            );
            $defaultvalues['subtitle'] = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('poster');
            file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_elang',
                'poster',
                0
            );
            $defaultvalues['poster'] = $draftitemid;

            $options = json_decode(isset($defaultvalues['options']) ? $defaultvalues['options'] : '{}', true);
            $defaultvalues['showlanguage'] = isset($options['showlanguage']) ? $options['showlanguage'] : true;
            $defaultvalues['repeatedunderscore'] = isset($options['repeatedunderscore']) ? $options['repeatedunderscore'] : 10;
            $defaultvalues['titlelength'] = isset($options['titlelength']) ? $options['titlelength'] : 100;
            $defaultvalues['limit'] = isset($options['limit']) ? $options['limit'] : 10;
            $defaultvalues['left'] = isset($options['left']) ? $options['left'] : 20;
            $defaultvalues['top'] = isset($options['top']) ? $options['top'] : 20;
            $defaultvalues['size'] = isset($options['size']) ? $options['size'] : 16;
            $defaultvalues['usetransliteration'] = isset($options['usetransliteration']) ? $options['usetransliteration'] : false;
            $defaultvalues['usecasesensitive'] = isset($options['usecasesensitive']) ? $options['usecasesensitive'] : false;
            $defaultvalues['jaroDistance'] = isset($options['jaroDistance']) ? $options['jaroDistance'] : 1;
            $defaultvalues['completion_gapfilled'] = isset($options['completion_gapfilled']) ? $options['completion_gapfilled'] : 0;
            $defaultvalues['completion_gapcompleted'] = isset(
                $options['completion_gapcompleted']) ? $options['completion_gapcompleted'] : 0;
            $defaultvalues['completion_gapfilledenabled'] = $defaultvalues['completion_gapfilled'] > 0;
            $defaultvalues['completion_gapcompletedenabled'] = $defaultvalues['completion_gapcompleted'] > 0;
        } else {
            $config = get_config('elang');
            $defaultvalues['repeatedunderscore'] = $config->repeatedunderscore;
            $defaultvalues['showlanguage'] = $config->showlanguage;
            $defaultvalues['titlelength'] = $config->titlelength;
            $defaultvalues['limit'] = $config->limit;
            $defaultvalues['left'] = $config->left;
            $defaultvalues['top'] = $config->top;
            $defaultvalues['size'] = $config->size;
            $defaultvalues['usetransliteration'] = $config->usetransliteration;
            $defaultvalues['usecasesensitive'] = $config->usecasesensitive;
            $defaultvalues['jaroDistance'] = $config->jaroDistance;
            $defaultvalues['completion_gapfilled'] = 0;
            $defaultvalues['completion_gapcompleted'] = 0;
            $defaultvalues['completion_gapfilledenabled'] = false;
            $defaultvalues['completion_gapcompletedenabled'] = false;
        }
    }

    /**
     * Perform minimal validation on the settings form
     *
     * @param   array  $data   Data to be validated
     * @param   array  $files  Files to be validated
     *
     * @return  array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // If no errors have been detecting on subtitle.
        if (!isset($errors['subtitle'])) {
            global $USER;
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            $files = $fs->get_area_files($context->id, 'user', 'draft', $data['subtitle'], 'id DESC', false);

            $noerror = false;

            // Loop on all files (normally only one).
            foreach ($files as $file) {
                // Save the file to a temporary path.
                try {
                    $filepath = $file->copy_content_to_temp();
                } catch (\Exception $e) {
                    $errors['subtitle'] = get_string('subtitleunabletosave', 'elang');
                    break;
                }

                // Try to detect encoding.
                $config = get_config('elang');

                if (isset($config->encodings)) {
                    $encodings = $config->encodings;
                } else {
                    $encodings = null;
                }

                $contents = Elang\transcode_subtitle(file_get_contents($filepath), 'UTF-8', $encodings);

                if (false === $contents) {
                    // Automatic detection does not succeed.
                    $errors['subtitle'] = get_string('subtitleunknownencoding', 'elang');
                    break;
                } else {
                    // Encoding succeeds, put back encoded contents in file.
                    file_put_contents($filepath, $contents);

                    $extension = pathinfo(unserialize($file->get_source())->source, PATHINFO_EXTENSION);

                    if ($extension === 'srt') {
                        // The extension is 'srt': it is a subrip file.
                        try {
                            $caption = new \Captioning\Format\SubripFile($filepath);
                            $caption->setUseIconv(function_exists('mb_convert_encoding'));
                            $noerror = true;
                            unset($errors['subtitle']);
                            break;
                        } catch (\Exception $e) {
                            $errors['subtitle'] = str_replace($filepath, 'file', $e->getMessage());
                        }
                    } else if ($extension === 'vtt') {
                        // The extension is 'vtt': it is a webvtt file.
                        try {
                            $caption = new \Captioning\Format\WebvttFile($filepath);
                            $caption->setUseIconv(function_exists('mb_convert_encoding'));
                            $noerror = true;
                            unset($errors['subtitle']);
                            break;
                        } catch (\Exception $e) {
                            $errors['subtitle'] = str_replace($filepath, 'file', $e->getMessage());
                        }
                    }
                }
            }

            if ($noerror) {
                // If $noerror is true, this means that automatic detection
                // of encoding has been successfull
                // and the file is in vtt or subrip format.
                $this->vtt = new \Captioning\Format\WebvttFile;

                // Construct the cues in vtt format.
                $cues = $caption->getCues();

                if ($cues) {
                    foreach ($cues as $cue) {
                        $this->vtt->addCue(
                            $cue->getText(),
                            \Captioning\Format\WebvttCue::ms2tc($cue->getStartMS()),
                            \Captioning\Format\WebvttCue::ms2tc($cue->getStopMS())
                        );
                    }
                }
            } else if (!isset($errors['subtitle'])) {
                // File is not in vtt or subrip format.
                $errors['subtitle'] = get_string('subtitleinvalidformat', 'elang');
            }
        }

        $jaro = str_replace(",", ".", $data['jaroDistance']);

        if (!is_numeric($jaro) || $jaro <= 0 || $jaro > 1) {
            $errors['jaroDistance'] = get_string('jaroDistance_error', 'elang');
        }

        return $errors;
    }
}
