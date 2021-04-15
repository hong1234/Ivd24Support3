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
            Benutzername: $email 
            Passwort:$passwort
            Ihre FTP-Zugangsdaten finden Sie unter: Mein IVD24 -> Meine Daten -> FTP-Zugangsdaten 
            Anleitungen zum Einrichten der Schnittstelle finden Sie unter: Mein IVD24 -> Meine Daten -> Hilfe/FAQ 
            Bei Fragen oder Anregungen können Sie uns gerne anrufen. Sie erreichen uns montags bis freitags von 9:00 bis 16:30 Uhr unter der 089 / 290 820 50. Den technischen Support erreichen Sie montags bis freitags von 9:00 bis 16:30 Uhr unter der 089 / 290 820 55 oder per E-Mail unter support@ivd24.de. Wir wünschen Ihnen gute Geschäfte und hoffen einen kleinen Beitrag mit ivd24.de leisten zu können.
            Mit freundlichen Grüßen
            Ihr ivd24 Team";
            $nachricht_html = $this->twig->render('email/new.makler.html.twig', [
                'email'    => $email,
                'passwort' => $passwort
            ]);
        }

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
            'sendername'      => $sendername, 
            'absender_mail'   => $absender_mail,
            'empfaenger_name' => $empfaenger_name,
            'empfaenger_mail' => $empfaenger_mail,
            'betreff'         => $betreff,
            'nachricht_html'  => $nachricht_html,
            'nachricht_plain' => $nachricht_plain,
            'insertdate'      => $insertdate
        ]);

    }

}