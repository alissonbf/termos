Instruções de instalação

1 - Coloque a pasta do modulo descompactada, dentro da pasta mod.

2 - No moodle vá em administração do site/avisos

3 - Peça para instalar o modulo.

4 - Abra o arquivo lib/moodlelib.php

5 - Ache esta linha:

    // Check that the user has agreed to a site policy if there is one - do not test in case of admins
    if (!$USER->policyagreed and !is_siteadmin()) {
        if (!empty($CFG->sitepolicy) and !isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($CFG->wwwroot .'/user/policy.php');
        } else if (!empty($CFG->sitepolicyguest) and isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($CFG->wwwroot .'/user/policy.php');
        }
    }

6 - Acima dela coloque este codigo    

    /**
     * Virifica se o usuario aceitou as politicas de uso do curso     
     *
     * @package    mod
     * @subpackage termos
     * @copyright  2012 Alisson Barbosa Ferreira
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
   
    $termos  = $DB->get_record('termos', array('course'=>$course->id));
    if($termos){
        $usuario = $DB->get_record('termos_agreed', array('user_id'=>$USER->id,'termos_id'=>$termos->id));
        
        if($usuario){
            if (!$usuario->agreed and !is_siteadmin()) {
                redirect($CFG->wwwroot .'/mod/termos/policy.php?course_id='.$course->id);
            } else {
                $SESSION->wantsurl = qualified_me();        
            }
        } else {
            redirect($CFG->wwwroot .'/mod/termos/policy.php?course_id='.$course->id);
        }
        $SESSION->wantsurl = qualified_me();
    }
    
    /*------------------------------------------------------------------------*/

7 - Agora adicione o modulo "termos" aos cursos onde o aluno deve aceitar um 
    contrato de uso do curso.