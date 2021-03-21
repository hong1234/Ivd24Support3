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

    public function addToSendQueue($modus, $data=[]){

        $username = $data['username'];
        $email    = $data['email'];
        $passwort = $data['passwort'];
        $geschaeftsstelle = '';
        if(isset($data['geschaeftsstelle'])){
            $geschaeftsstelle = $data['geschaeftsstelle'];
        }

        $sendername      = 'Ivd24Admin';
        $absender_mail   = 'noreply@ivd24immobilien.de';
        $empfaenger_name = $username;
        $empfaenger_mail = $email;
        $insertdate      = time();

        $betreff  = '';
        $nachricht_plain = '';
        $nachricht_html  = '';

        if($modus=='supporter_new'){
            $betreff  = 'You are registered as Supporter !';
            $nachricht_plain = "You are registered as Supporter! with username=$username ; email=$email ; passwort=$passwort";
            $nachricht_html = $this->twig->render('email/new.email.html.twig', [
                'username'  => $username,
                'email'     => $email,
                'passwort'  => $passwort
            ]);
        }

        if($modus=='supporter_edit'){
            $betreff         = 'Your data are updated!';
            $nachricht_plain = "Your data are updated! with (new)username=$username ; (new)email=$email ; (new)passwort=$passwort";
            $nachricht_html = $this->twig->render('email/update.email.html.twig', [
                'username'  => $username,
                'email'     => $email,
                'passwort'  => $passwort
            ]); 
        }

        if($modus=='statisticuser_new'){
            $betreff  = 'You are registered as Statistic-User!';
            $nachricht_plain = "You are registered as Statistic-User! with username=$username ; email=$email ; passwort=$passwort ; geschaeftsstelle=$geschaeftsstelle";
            $nachricht_html = $this->twig->render('email/sta.new.email.html.twig', [
                'username'  => $username,
                'email'     => $email,
                'passwort'  => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);
        }

        if($modus=='statisticuser_edit'){
            $betreff         = 'Your data are updated!';
            $nachricht_plain = "Your data are updated! with (new)username=$username ; (new)email=$email ; (new)passwort=$passwort ; (new)geschaeftsstelle=$geschaeftsstelle";
            $nachricht_html = $this->twig->render('email/sta.update.email.html.twig', [
                'username'  => $username,
                'email'     => $email,
                'passwort'  => $passwort,
                'geschaeftsstelle' => $geschaeftsstelle
            ]);  
        }
        
        $this->bDao->insertSendQueue([
            'sendername'        => $sendername, 
            'absender_mail'     => $absender_mail,
            'empfaenger_name'   => $empfaenger_name,
            'empfaenger_mail'   => $empfaenger_mail,
            'betreff'           => $betreff,
            'nachricht_html'    => $nachricht_html,
            'nachricht_plain'   => $nachricht_plain,
            'insertdate'        => $insertdate
        ]);

    }

}