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

        $username = '';
        $email = '';
        $passwort = '';
        $geschaeftsstelle = '';

        if(isset($data['username'])){
            $username = $data['username'];
        }

        if(isset($data['email'])){
            $email = $data['email'];
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
            $betreff  = "Sie sind registriert as Makler!";
            $nachricht_plain = "Liebes Mitglied, 
            wir freuen uns, Sie beim verbandseigenen Immobilienportal ivd24 begrüßen zu dürfen! Bevor es losgeht, möchten wir Ihnen hier unsere Services noch einmal kurz vorstellen.
            Neben der klassischen Angebotsplattform zur Vermarktung Ihrer Immobilien, profitieren Sie bei ivd24 von vielen weiteren Mehrwerten:
            • dem ivd24-Grundrissservice 
            • der IVD-Expertensuche 
            • dem Immo-Profi-Texter 
            • der Börsenfunktionalität für Gemeinschaftsgeschäfte 
            • unterschiedlichen Druckformaten für Exposés (inkl. Messe-Banner-Druck) 
            • der Schufa-Bonitätsauskunft 
            • der Objektverwaltung für die eigene Webseite
            Ausführliche Informationen zu diesen und weiteren Services erhalten Sie im angefügten Tutorial.
            Um Ihnen neben all diesen Mehrwerten noch weitere Funktionalitäten und Partnerschaften anbieten zu können, haben wir unseren ivd24-Business-Club ins Leben gerufen. Hier bündeln wir viele weitere professionelle Lösungen für Sie zu günstigen Pauschalpreisen. Wir möchten Sie damit unterstützen, Ihren Weg zum digitalen Makler voranzutreiben und dadurch Ihre Akquise zu stärken. Lesen Sie mehr unter 
            https://www.ivd24immobilien.de/newsletter-businessclub?returncode=EMAIADRESSE-AUS-USER_MAKLER_CONFIG-returncode
            Ein attraktives Partnerangebot, das wir Ihnen im ivd24-Business-Club anbieten, ist beispielsweise StoryBox. Informationen dazu finden sie unter https://www.ivd24immobilien.de/newsletter-storybox
            Doch nun genug der Information. Jetzt kann es losgehen! Vervollständigen Sie Ihre Daten mit Logo und Schwerpunkten noch heute und schalten Sie sich für die Suche frei, um möglichst schnell von unserem Service zu profitieren.
            Nutzen Sie dazu folgende Zugangsdaten:
            Benutzername: $username 
            Passwort:$passwort
            Ihre FTP-Zugangsdaten finden Sie unter: Mein IVD24 -> Meine Daten -> FTP-Zugangsdaten 
            Anleitungen zum Einrichten der Schnittstelle finden Sie unter: Mein IVD24 -> Meine Daten -> Hilfe/FAQ 
            Bei Fragen oder Anregungen können Sie uns gerne anrufen. Sie erreichen uns montags bis freitags von 9:00 bis 16:30 Uhr unter der 089 / 290 820 50. Den technischen Support erreichen Sie montags bis freitags von 9:00 bis 16:30 Uhr unter der 089 / 290 820 55 oder per E-Mail unter support@ivd24.de. Wir wünschen Ihnen gute Geschäfte und hoffen einen kleinen Beitrag mit ivd24.de leisten zu können.
            Mit freundlichen Grüßen
            Ihr ivd24 Team";
            $nachricht_html = $this->twig->render('email/new.makler.html.twig', [
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

    public function addToSendQueue2($mode, $data=[]) {

        $username = '';
        $email = '';
        $betreff = '';
        $template_id = '';
        $user_id = '0';

        $anrede  = '';
        $vorname = '';


        if(isset($data['template_id'])){
            $template_id = (int)$data['template_id'];
        }

        if(isset($data['betreff'])){
            $betreff = $data['betreff'];
        }

        if(isset($data['email'])){
            $email = $data['email'];
        }

        if(isset($data['anrede'])){
            $anrede = $data['anrede'];
        }

        if(isset($data['vorname'])){
            $vorname = $data['vorname'];
        }

        if(isset($data['name'])){
            $name = $data['name'];
        }

        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }
        

        $sendername      = 'IVD24Immobilien';
        $absender_mail   = 'noreply@ivd24immobilien.de';
        $reply_mail      = 'noreply@ivd24immobilien.de';
        $empfaenger_name = $name;
        $empfaenger_mail = $email;

        $nachricht_plain = '';

        $row = $this->bDao->getRowInTableByIdentifier('send_mail_templates', [
            'mail_template_id' => $template_id
        ]);
        $nachricht_html = $row['nachricht'];

        $nachricht_html = str_replace(
            ["{{anrede}}", "{{vorname}}", "{{nachname}}", "{{user_id}}"], 
            [$anrede, $vorname, $name, $user_id], 
            $nachricht_html
        );

        if($mode == 'mode1'){
            //
        }

        if($mode == 'mode2'){
            //
        }

        $this->bDao->insertSendQueue([
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