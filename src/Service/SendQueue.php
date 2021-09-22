<?php
namespace App\Service;

use Twig\Environment;
use App\Dao\BaseDao;

class SendQueue
{
    private $bDao;
    private $twig;

    function __construct(BaseDao $bDao, Environment $twig) {
        $this->bDao = $bDao;
        $this->twig = $twig;
    }

    public function addToSendQueue($modus, $data=[]) {

        $user_id = $data['user_id'];
        $email = $data['email'];
        // $email = '';
        // if(isset($data['email'])){
        //     $email = $data['email'];
        // }
        
        $anrede   = '';
        $titel    = '';
        $vorname  = '';
        $nachname = '';
        $geschaeftsstelle = '';

        $username = '';
        $passwort = '';
        
        if(isset($data['anrede'])){
            $anrede = $data['anrede'];
        }

        if(isset($data['titel'])){
            $titel = $data['titel'];
            if($titel != ''){
                $titel = $titel.",";
            }
        }

        if(isset($data['vorname'])){
            $vorname = $data['vorname'];
        }

        if(isset($data['nachname'])){
            $nachname = $data['nachname'];
        }

        if(isset($data['username'])){
            $username = $data['username'];
        }

        if(isset($data['passwort'])){
            $passwort = $data['passwort'];
        }
        
        if(isset($data['geschaeftsstelle'])){
            $geschaeftsstelle = $data['geschaeftsstelle'];
        }

        $sendername      = 'IVD24Immobilien';
        $absender_mail   = 'noreply@ivd24immobilien.de';
        $reply_mail      = 'noreply@ivd24immobilien.de';
        $empfaenger_name = $username;
        $empfaenger_mail = $email;
        $insertdate      = time();

        $betreff  = '';
        $nachricht_plain = '';
        $nachricht_html  = '';

        if($modus=='makler_new'){
            $betreff  = "Ihr Account bei ivd24immobilien.de wurde freigeschaltet";
            $nachricht_plain = "sehen html text";
            
            $nachricht_html = $this->twig->render('email/new.makler.html.twig', [
                'anrede'   => $anrede,
                'titel'    => $titel,
                'vorname'  => $vorname,
                'nachname' => $nachname,
                'username' => $username,
                'passwort' => $passwort
            ]);
        }

        if($modus=='makler_edit_pw'){
            $betreff = 'Ihre Passwort wurde geändert!';
            $nachricht_plain = "Ihre Passwort wurde geändert! Ihre neue Passwort: $passwort";
            
            $nachricht_html = $this->twig->render('email/pw.edit.makler.html.twig', [
                'passwort' => $passwort
            ]);
        }

        if($modus=='makler_edit_ftp_pw'){
            $betreff  = 'Ihre FTP-Passwort wurde geändert!';
            $nachricht_plain = "Ihre FTP-Passwort wurde geändert! Ihre neue FTP-Passwort: $passwort";
            
            $nachricht_html = $this->twig->render('email/ftppw.edit.makler.html.twig', [
                'passwort' => $passwort
            ]);
        }

        if($modus=='supporter_new'){
            $betreff  = 'Sie sind registriert als Supporter!';
            $nachricht_plain = "Sie sind registriert als Supporter! mit username=$username ; email=$email ; passwort=$passwort";
            
            $nachricht_html = $this->twig->render('email/new.supporter.html.twig', [
                'username' => $username,
                'email'    => $email,
                'passwort' => $passwort
            ]);
        }

        if($modus=='supporter_edit'){
            $betreff         = 'Ihre Daten ist updated!';
            $nachricht_plain = "Ihre Daten ist updated! mit (neu)username=$username ; (neu)email=$email ; (neu)passwort=$passwort";
            
            $nachricht_html = $this->twig->render('email/edit.supporter.html.twig', [
                'username' => $username,
                'email'    => $email,
                'passwort' => $passwort
            ]); 
        }

        if($modus=='statisticuser_new'){
            $betreff  = 'Sie sind registriert als Statistic-User!';
            $nachricht_plain = "Sie sind registriert als Statistic-User! mit username=$username ; email=$email ; passwort=$passwort ; geschaeftsstelle=$geschaeftsstelle";
            
            $nachricht_html = $this->twig->render('email/new.statisticuser.html.twig', [
                'username' => $username,
                'email'    => $email,
                'passwort' => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);
        }

        if($modus=='statisticuser_edit'){
            $betreff         = 'Ihre Daten ist updated!';
            $nachricht_plain = "Ihre Daten ist updated! mit (neu)username=$username ; (neu)email=$email ; (neu)passwort=$passwort ; (neu)geschaeftsstelle=$geschaeftsstelle";
            
            $nachricht_html = $this->twig->render('email/edit.statisticuser.html.twig', [
                'username' => $username,
                'email'    => $email,
                'passwort' => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);  
        }
        
        $this->bDao->insertSendQueue([
            'user_id'         => $user_id,
            'sendername'      => $sendername, 
            'absender_mail'   => $absender_mail,
            'reply_mail'      => $reply_mail,
            'empfaenger_name' => $empfaenger_name,
            'empfaenger_mail' => $empfaenger_mail,
            'betreff'         => $betreff,
            'nachricht_html'  => $nachricht_html,
            'nachricht_plain' => $nachricht_plain,
            'insertdate'      => $insertdate
        ]);

    }

    public function addToSendQueue2($data=[]) {
        $hauptversammlung_id = $data['versammlung_id'];
        $user_id = $data['user_id'];
        $user_id_md5 = md5($user_id);
        // $anmeldelink = "https://www.ivd24immobilien.de/anmelden-hv/?hvid=$hauptversammlung_id&auth=$user_id_md5";
        $anmeldelink = '<a href="https://www.ivd24immobilien.de/anmelden-hv/?hvid='.$hauptversammlung_id.'&auth='.$user_id_md5.'">Link zur Anmeldung</a>';

        $email = $data['email'];
        $betreff = $data['betreff'];
        $template = $data['template'];
        
        $briefanrede = '';
        $anrede  = '';
        $vorname = '';
        $name = '';

        $anhang_datei = '';
        $anhang_datei_jn = 'N';
        $anhang_datei_data = '';
        
        if(isset($data['anrede'])){
            $anrede = $data['anrede'];
        }

        if(isset($data['vorname'])){
            $vorname = $data['vorname'];
        }

        if(isset($data['name'])){
            $name = $data['name'];
        }

        if (trim($anrede) == 'Frau'){
            $briefanrede = "Sehr geehrte Frau $name,";
        } elseif (trim($anrede) == 'Herr'){
            $briefanrede = "Sehr geehrter Herr $name,";
        } else {
            $briefanrede = "Sehr geehrte/r Divers $name,";
        }

        $nachricht_html = $template->nachricht;
        $anhang_datei   = (string)$template->document_path;

        $sendername      = 'IVD24Immobilien';
        $absender_mail   = 'noreply@ivd24immobilien.de';
        $reply_mail      = 'noreply@ivd24immobilien.de';
        $empfaenger_name = $name;
        $empfaenger_mail = $email;

        $nachricht_plain = 'see html text';
        $nachricht_html  = $this->substitutePlaceholder($briefanrede, $anrede, $vorname, $name, $user_id, $anmeldelink, $nachricht_html);

        if(strlen($anhang_datei) > 0){

            $anhang_datei_jn = 'J';
            $anhang_datei_data = '/var/www/html/dokumente/ivd24';

            $this->bDao->insertSendQueue2([
                'user_id'         => $user_id,
                'sendername'      => $sendername,
                'absender_mail'   => $absender_mail,
                'reply_mail'      => $reply_mail,
                'empfaenger_name' => $empfaenger_name,
                'empfaenger_mail' => $empfaenger_mail,
                'betreff'         => $betreff,
                'nachricht_html'  => $nachricht_html,
                'nachricht_plain' => $nachricht_plain,
                'anhang_datei_jn' => $anhang_datei_jn,
                'anhang_datei'    => $anhang_datei,
                'anhang_datei_data' => $anhang_datei_data,
                'insertdate'      => time()
            ]);

        } else {

            $this->bDao->insertSendQueue([
                'user_id'         => $user_id,
                'sendername'      => $sendername, 
                'absender_mail'   => $absender_mail,
                'reply_mail'      => $reply_mail,
                'empfaenger_name' => $empfaenger_name,
                'empfaenger_mail' => $empfaenger_mail,
                'betreff'         => $betreff,
                'nachricht_html'  => $nachricht_html,
                'nachricht_plain' => $nachricht_plain,
                'insertdate'      => time()
            ]);

        }

    }

    public function substitutePlaceholder($briefanrede, $anrede, $vorname, $name, $user_id, $anmeldelink, $nachricht_html) {
        $nachricht_html = str_replace(
            ["{{briefanrede}}", "{{anrede}}", "{{vorname}}", "{{nachname}}", "{{user_id}}", "{{anmeldelink}}"], 
            [$briefanrede, $anrede, $vorname, $name, $user_id, $anmeldelink], 
            $nachricht_html
        );

        return $nachricht_html;
    }

}