<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

require_once('modules/Settings/MailConverter/handlers/MailScannerAction.php');

/**
 * Scanner Rule
 */
class Vtiger_MailScannerRule {
    // id of this instance
	var $ruleid    = false;
    // scanner to which this rule is linked
    var $scannerid = false;
    // from address criteria
	var $fromaddress= false;
    // to address criteria
    var $toaddress = false;
    // cc address criteria
    var $cc = false;
    // bcc address criteria
    var $bcc = false;
    // subject criteria operator
    var $subjectop = false;
    // subject criteria
	var $subject   = false;
    // body criteria operator
	var $bodyop    = false;
    // body criteria
	var $body      = false;
    // order of this rule
	var $sequence  = false;
    // is this action valid
	var $isvalid   = false;
    // match criteria ALL or ANY
    var $matchusing = false;
    // assigned to user id
    var $assigned_to = false;
    // associated actions for this rule
	var $actions  = false;
    // TODO we are restricting one action for one rule right now
	var $useaction= false;

	/** DEBUG functionality */
	var $debug		= false;
	function log($message) {
		global $log;
		if($log && $this->debug) { $log->debug($message); }
		else if($this->debug) echo "$message\n";
	}

    /**
     * Constructor
     */
    function __construct($forruleid) {
        $this->initialize($forruleid);
    }

    /**
     * String representation of this instance
     */
    function __toString() {
        $tostring = '';
        $tostring .= "FROM $this->fromaddress, TO $this->toaddress, CC $this->cc, BCC $this->bcc";
        $tostring .= ",SUBJECT $this->subjectop $this->subject, BODY $this->bodyop $this->body, MATCH USING, $this->matchusing";
        return $tostring;
    }

    /**
     * Initialize this instance
     */
    function initialize($forruleid) {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_mailscanner_rules WHERE ruleid=? ORDER BY sequence", Array($forruleid));

        if ($adb->num_rows($result)) {
            $this->ruleid = $adb->query_result($result, 0, 'ruleid');
            $this->scannerid = $adb->query_result($result, 0, 'scannerid');
            $this->fromaddress = $adb->query_result($result, 0, 'fromaddress');
            $this->toaddress = $adb->query_result($result, 0, 'toaddress');
            $this->cc = $adb->query_result($result, 0, 'cc');
            $this->bcc = $adb->query_result($result, 0, 'bcc');
            $this->subjectop = $adb->query_result($result, 0, 'subjectop');
            $this->subject = $adb->query_result($result, 0, 'subject');
            $this->bodyop = $adb->query_result($result, 0, 'bodyop');
            $this->body = $adb->query_result($result, 0, 'body');
            $this->sequence = $adb->query_result($result, 0, 'sequence');
            $this->matchusing = $adb->query_result($result, 0, 'matchusing');
            $this->assigned_to = $adb->query_result($result, 0, 'assigned_to');
            $this->isvalid = true;
            $this->initializeActions();
            // At present we support only one action for a rule
                if(!empty($this->actions)) $this->useaction = $this->actions[0];
        }
    }

    /**
     * Initialize the actions
     */
    function initializeActions() {
        global $adb;
            if($this->ruleid) {
            $this->actions = Array();
                $actionres = $adb->pquery("SELECT actionid FROM vtiger_mailscanner_ruleactions WHERE ruleid=?",Array($this->ruleid));
            $actioncount = $adb->num_rows($actionres);
                if($actioncount) {
                    for($index = 0; $index < $actioncount; ++$index) {
                $actionid = $adb->query_result($actionres, $index, 'actionid');
                $ruleaction = new Vtiger_MailScannerAction($actionid);
                $ruleaction->debug = $this->debug;
                $this->actions[] = $ruleaction;
            }
            }
        }
    }

    /**
     * Is body rule defined?
     */
    function hasBodyRule() {
        return (!empty($this->bodyop));
    }

    /**
     * Check if the rule criteria is matching
     */
	function isMatching($matchfound1, $matchfound2 = null) {
        if ($matchfound2 === null)
            return $matchfound1;

        if ($this->matchusing == 'AND')
            return ($matchfound1 && $matchfound2);
        if ($this->matchusing == 'OR')
            return ($matchfound1 || $matchfound2);
        return false;
    }

    /**
     * Apply all the criteria.
     * @param $mailrecord
     * @param $includingBody
     * @returns false if not match is found or else all matching result found
     */
    function applyAll($mailrecord, $includingBody=true) {
        $this->log("start Applyall");
        $matchresults = Array();
        $matchfound = null;

        if ($this->hasACondition()) {
            $subrules = Array('FROM', 'TO', 'CC', 'BCC', 'SUBJECT', 'BODY');

            foreach ($subrules as $subrule) {
            // Body rule could be defered later to improve performance
            // in that case skip it.
                    if($subrule == 'BODY' && !$includingBody) continue;

            $checkmatch = $this->apply($subrule, $mailrecord);
            $matchfound = $this->isMatching($checkmatch, $matchfound);
            // Collect matching result array
            if ($matchfound && is_array($checkmatch))
                $matchresults[] = $checkmatch;
            }
        } else {
            $matchfound = false;
                if($this->matchusing == 'OR') {
            $matchfound = true;
                    $matchresults[] = $this->__CreateMatchResult('BLANK','','','');
            }
        }
		return ($matchfound)? $matchresults : false;
    }

    /**
     * Check if at least one condition is set for this rule.
     */
    function hasACondition() {
        $hasFromAddress = $this->fromaddress ? true : false;
        $hasToAddress = $this->toaddress ? true : false;
        $hasCcAddress = $this->cc ? true : false;
        $hasBccAddress = $this->bcc ? true : false;
        $hasSubjectOp = $this->subjectop ? true : false;
        $hasBodyOp = $this->bodyop ? true : false;
        return ($hasFromAddress || $hasToAddress || $hasCcAddress || $hasBccAddress || $hasSubjectOp || $hasBodyOp);
    }

    /**
     * Apply required condition on the mail record.
     */
    function apply($subrule, $mailrecord) {
        $this->log("Start subrule");
        $matchfound = false;
            if($this->isvalid) {
                switch(strtoupper($subrule)) {
            case 'FROM':
                if($this->fromaddress) {
                    if(strpos($this->fromaddress, '*') == 0)
                        $this->fromaddress = trim($this->fromaddress, '*');
                    $matchfound = $this->find($subrule, 'Contains', $mailrecord->_from[0], $this->fromaddress);
                } else {
                    $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            case 'TO':
                        if($this->toaddress) {
                            foreach($mailrecord->_to as $toemail) {
                    $matchfound = $this->find($subrule, 'Contains', $toemail, $this->toaddress);
                                if($matchfound) break;
                }
                } else {
                $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            case 'CC':
                if ($this->cc) {
                foreach ($mailrecord->_cc as $toemail) {
                    $matchfound = $this->find($subrule, 'Contains', $toemail, $this->cc);
                    if ($matchfound)
                    break;
                }
                } else {
                $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            case 'BCC':
                if ($this->bcc) {
                foreach ($mailrecord->_bcc as $toemail) {
                    $matchfound = $this->find($subrule, 'Contains', $toemail, $this->bcc);
                    if ($matchfound)
                    break;
                }
                } else {
                $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            case 'SUBJECT':
                if ($this->subjectop) {
                    $matchfound = $this->find($subrule, $this->subjectop, $mailrecord->_subject, $this->subject);
                } else {
                    $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            case 'BODY':
                if ($this->bodyop) {
                $matchfound = $this->find($subrule, $this->bodyop, trim(strip_tags($mailrecord->_body)), trim($this->body));
                } else {
                $matchfound = $this->__CreateDefaultMatchResult($subrule);
                }
                break;
            }
        }
        return $matchfound;
    }

    /**
     * Find if the rule matches based on condition and parameters
     */
    function find($subrule, $condition, $input, $searchfor) {
        $this->log("Starting Match Rule");
        if (!$input)
            return false;
            $input = trim(preg_replace("/\r/", '', decode_html($input))); 
            $searchfor = decode_html($searchfor);
        $matchfound = false;
        $matches = false;

        switch ($condition) {
            case 'Contains':
            $matchfound = stripos($input, $searchfor);
            $matchfound = ($matchfound !== FALSE);
            $matches = $searchfor;
            break;
            case 'Not Contains':
            $matchfound = stripos($input, $searchfor);
            $matchfound = ($matchfound === FALSE);
            $matches = $searchfor;
            break;
            case 'Equals':
            $matchfound = strcasecmp($input, $searchfor);
            $matchfound = ($matchfound === 0);
            $matches = $searchfor;
            break;
            case 'Not Equals':
            $matchfound = strcasecmp($input, $searchfor);
            $matchfound = ($matchfound !== 0);
            $matches = $searchfor;
            break;
            case 'Begins With':
            $matchfound = stripos($input, $searchfor);
            $matchfound = ($matchfound === 0);
            $matches = $searchfor;
            break;
            case 'Ends With':
            $matchfound = strripos($input, $searchfor);
            $matchfound = ($matchfound === strlen($input) - strlen($searchfor));
            $matches = $searchfor;
            break;
            case 'Regex':
            $regmatches = Array();
            $matchfound = false;
            $searchfor = str_replace('/', '\/', $searchfor);
            $input = str_replace("_", " ", $input);
            if (preg_match("/$searchfor/i", $input, $regmatches)) {
                // Pick the last matching group
                $matches = $regmatches[count($regmatches) - 1];
                $matchfound = true;
            }
            break;
            case 'Has Ticket Number':
            $regmatches = Array();
            $matchfound = false;
            $searchfor = "Ticket Id[^:]?: ([0-9]+)"; 
            $searchfor = str_replace('/', '\/', $searchfor);
            if (preg_match("/$searchfor/i", $input, $regmatches)) {
                // Pick the last matching group
                $matches = $regmatches[count($regmatches) - 1];
                $this->log($matches);
                $matchfound = true;
            }
            break;
        }
        if($matchfound) $matchfound = $this->__CreateMatchResult($subrule, $condition, $searchfor, $matches);
        return $matchfound;
    }

    /**
     * Create matching result for the subrule.
     */
    function __CreateMatchResult($subrule, $condition, $searchfor, $matches) {
		return Array( 'subrule' => $subrule, 'condition' => $condition, 'searchfor' => $searchfor, 'matches' => $matches);
    }

    /**
     * Create default success matching result
     */
    function __CreateDefaultMatchResult($subrule) {
		if($this->matchusing == 'OR') return false;
		if($this->matchusing == 'AND') return $this->__CreateMatchResult($subrule, 'Contains', '', '');
    }

    /**
     * Detect if the rule match result has Regex condition
     * @param $matchresult result of apply obtained earlier
     * @returns matchinfo if Regex match is found, false otherwise
     */
    function hasRegexMatch($matchresult) {
		foreach($matchresult as $matchinfo) {
            $match_condition = $matchinfo['condition'];
            $match_string = $matchinfo['matches'];
                if(($match_condition == 'Regex' || $match_condition == 'Has Ticket Number') && $match_string) 
            return $matchinfo;
        }
        return false;
    }

    /**
     * Swap (reset) sequence of two rules.
     */
    static function resetSequence($ruleid1, $ruleid2) {
        global $adb;
            $ruleresult = $adb->pquery("SELECT ruleid, sequence FROM vtiger_mailscanner_rules WHERE ruleid = ? or ruleid = ?",
                Array($ruleid1, $ruleid2));
        $rule_partinfo = Array();
            if($adb->num_rows($ruleresult) != 2) {
            return false;
        } else {
            $rule_partinfo[$adb->query_result($ruleresult, 0, 'ruleid')] = $adb->query_result($ruleresult, 0, 'sequence');
            $rule_partinfo[$adb->query_result($ruleresult, 1, 'ruleid')] = $adb->query_result($ruleresult, 1, 'sequence');
            $adb->pquery("UPDATE vtiger_mailscanner_rules SET sequence = ? WHERE ruleid = ?", Array($rule_partinfo[$ruleid2], $ruleid1));
            $adb->pquery("UPDATE vtiger_mailscanner_rules SET sequence = ? WHERE ruleid = ?", Array($rule_partinfo[$ruleid1], $ruleid2));
        }
    }

    /**
     * Update rule information in database.
     */
    function update() {
        global $adb;
        if ($this->ruleid) {
            $adb->pquery("UPDATE vtiger_mailscanner_rules SET scannerid=?,fromaddress=?,toaddress=?,subjectop=?,subject=?,bodyop=?,body=?,matchusing=?,assigned_to=?,cc=?,bcc=?
                    WHERE ruleid=?", Array($this->scannerid, $this->fromaddress, $this->toaddress, $this->subjectop, $this->subject,
            $this->bodyop, $this->body, $this->matchusing, $this->assigned_to, $this->cc, $this->bcc, $this->ruleid));
        } else {
            $this->sequence = $this->__nextsequence();
            $adb->pquery("INSERT INTO vtiger_mailscanner_rules(scannerid,fromaddress,toaddress,subjectop,subject,bodyop,body,matchusing,sequence,assigned_to,cc,bcc)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)", Array($this->scannerid, $this->fromaddress, $this->toaddress, $this->subjectop, $this->subject,
            $this->bodyop, $this->body, $this->matchusing, $this->sequence, $this->assigned_to, $this->cc, $this->bcc));
            $this->ruleid = $adb->database->Insert_ID();
        }
    }

    /**
     * Get next sequence to use
     */
    function __nextsequence() {
        global $adb;
        $seqres = $adb->pquery("SELECT max(sequence) AS max_sequence FROM vtiger_mailscanner_rules", Array());
        $maxsequence = 0;
            if($adb->num_rows($seqres)) {
            $maxsequence = $adb->query_result($seqres, 0, 'max_sequence');
        }
        ++$maxsequence;
        return $maxsequence;
    }

    /**
     * Delete the rule and associated information.
     */
    function delete() {
        global $adb;

        // Delete dependencies
            if(!empty($this->actions)) {
                foreach($this->actions as $action) {
            $action->delete();
            }
        }
            if($this->ruleid) {
            $adb->pquery("DELETE FROM vtiger_mailscanner_ruleactions WHERE ruleid = ?", Array($this->ruleid));
            $adb->pquery("DELETE FROM vtiger_mailscanner_rules WHERE ruleid=?", Array($this->ruleid));
        }
    }

    /**
     * Update action linked to the rule.
     */
    function updateAction($actionid, $actiontext) {
        $action = $this->useaction;

            if($actionid != '' && $actiontext == '') {
                if($action) $action->delete();
        } else {
                if($actionid == '') {
            $action = new Vtiger_MailScannerAction($actionid);
            }
            $action->scannerid = $this->scannerid;
            $action->update($this->ruleid, $actiontext);
        }
    }

    /**
     * Take action on mail record
     */
    function takeAction($mailscanner, $mailrecord, $matchresult) {
		if(empty($this->actions)) return false;

        $action = $this->useaction; // Action is limited to One right now
        return $action->apply($mailscanner, $mailrecord, $this, $matchresult);
    }

}

?>
