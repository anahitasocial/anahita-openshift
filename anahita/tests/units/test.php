<?php 

class StackTest extends PHPUnit_Framework_TestCase
{
    public function _testComposer()
    {        
        $session = get_session();
        global $user;        
        $session->open(get_url('option=com_stories&view=stories&oid='.$user->id));
        if ( ! $composer = $session->element('css selector','#story-composer textarea') ) {
            $this->fail('Story composer not found');
        }

        $this->uniqid = uniqid();
        $composer->value(split_keys('test story '.$this->uniqid));        
        $shareBtn = $session->element('css selector','#story-composer button[data-trigger="Share"]');        
        if ( !$shareBtn ) {
            $this->fail('Share button not found');
        }
        $shareBtn->click();
    }
    
    public function _testComment()
    {
        $session  = get_session();       
        if ( !function_exists('comment_on_story') )
        {
            function comment_on_story()
            {
                $session  = get_session();
                $session->activeElement()->value(split_keys('commenting on story'));
                $form = $session->activeElement()->element('xpath','..');
                $form->element('css selector','.comment-form-container button')->click();
                $session->element('css selector','a.delete-comment')->click();
                $session->element('css selector','.modal-footer .danger')->click();            
            }
        }

        $session->element('css selector', 'a.comment')->click();            
        comment_on_story();        
        $session->element('css selector','.action-comment-overtext')->click();
        //comment_on_story();
    }
    
    public function testCreateGroup()
    {
        $session  = get_session();
        $session->open(get_url('option=com_groups&view=groups'));
        $session->element('css selector','.actions .btn')->click();
        fill_out_form('.component-content form',array(
            'name'     => 'Group name',
            'body'     => 'Group body',
            'enabled'  => 'No'
        ))
        ->submit();
       
        element('css selector','a[href*="edit=apps"]')->click();
        element('css selector','.app-actions a[href*="app=com_todos"]')->click();
        element('css selector','.component-content .actor-avatar-link')->click();
    }
}

?>