<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Helpers disponibles partout (vues + contrôleurs), comme dans l'ancien projet.
        // IMPORTANT: à définir avant le parent::initController().
        $this->helpers = ['url', 'lang', 'translate'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Si ?lang= est fourni, on le persiste en cookie (30 jours) pour les pages suivantes.
        // (Logique reprise de l'ancien projet)
        $lang = $this->request->getGet('lang');
        if ($lang && in_array($lang, ['fr', 'en'], true)) {
            setcookie('site_lang', $lang, time() + 60 * 60 * 24 * 30, '/');
            // dispo immédiatement sur la requête courante
            $_COOKIE['site_lang'] = $lang;
        }

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Génère un email HTML Premium aux couleurs de Kayart.
     * Aligné avec l'identité artisanale, performance & composite carbone.
     * 
     * @param string $title Le titre principal (ex: Votre projet KAYART)
     * @param string $content Le contenu HTML du message
     * @param string|null $ctaUrl (Optionnel) Lien du bouton d'action
     * @param string $ctaText (Optionnel) Texte du bouton
     */
    protected function getEmailTemplate(string $title, string $content, ?string $ctaUrl = null, string $ctaText = 'Discuter de votre projet')
    {
        // Palette KAYART : Performance & Artisanat
        $bgBody = '#e8eaed';           // Gris clair texturé (carbone discret)
        $bgContent = '#ffffff';        // Blanc pur (carte centrale)
        $bgAccent = '#f8f9fa';         // Blanc cassé (zones secondaires)
        $colorText = '#1e293b';        // Anthracite profond (texte principal)
        $colorPrimary = '#0f172a';     // Bleu nuit intense (titres)
        $colorAccent = '#d97706';      // Orange cuivre (artisanat, chaleur)
        $colorMuted = '#64748b';       // Gris moyen (textes secondaires)
        $borderSubtle = '#cbd5e1';     // Bordures discrètes

        // Bouton d'action (call-to-action qualitatif)
        $buttonHtml = '';
        if ($ctaUrl) {
            $buttonHtml = "
                <div style='text-align: center; margin: 35px 0 25px 0;'>
                    <a href='{$ctaUrl}' style='
                        background: linear-gradient(135deg, {$colorPrimary} 0%, #1e293b 100%);
                        color: #ffffff;
                        padding: 16px 32px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-family: \"Segoe UI\", Helvetica, Arial, sans-serif;
                        font-weight: 600;
                        font-size: 15px;
                        border: 2px solid {$colorAccent};
                        display: inline-block;
                        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
                        transition: all 0.3s ease;
                    '>
                        ✦ {$ctaText}
                    </a>
                </div>
            ";
        }

        // Structure HTML Email KAYART
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
        </head>
        <body style='
            margin: 0;
            padding: 0;
            background-color: {$bgBody};
            background-image: linear-gradient(135deg, {$bgBody} 0%, #f1f5f9 100%);
            font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        '>
            
            <div style='max-width: 640px; margin: 0 auto; padding: 40px 20px;'>
                
                <!-- HEADER FORT -->
                <div style='text-align: center; margin-bottom: 35px;'>
                    <div style='
                        background: linear-gradient(135deg, {$colorPrimary} 0%, #1e293b 100%);
                        padding: 25px 20px;
                        border-radius: 8px 8px 0 0;
                        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
                    '>
                        <h1 style='
                            color: #ffffff;
                            font-family: \"Georgia\", \"Times New Roman\", serif;
                            text-transform: uppercase;
                            letter-spacing: 4px;
                            font-size: 32px;
                            margin: 0 0 8px 0;
                            font-weight: 700;
                        '>KAYART</h1>
                        <p style='
                            color: {$colorAccent};
                            font-size: 13px;
                            text-transform: uppercase;
                            letter-spacing: 2px;
                            margin: 0;
                            font-weight: 500;
                        '>L'artisan du composite carbone</p>
                    </div>
                </div>

                <!-- CONTENEUR PRINCIPAL -->
                <div style='
                    background-color: {$bgContent};
                    border-radius: 0 0 8px 8px;
                    padding: 0;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                    border: 1px solid {$borderSubtle};
                    border-top: 4px solid {$colorAccent};
                '>
                    
                    <!-- ACCROCHE ÉMOTIONNELLE -->
                    <div style='
                        background-color: {$bgAccent};
                        padding: 25px 30px;
                        border-bottom: 1px solid {$borderSubtle};
                    '>
                        <p style='
                            color: {$colorMuted};
                            font-size: 14px;
                            line-height: 1.6;
                            margin: 0;
                            font-style: italic;
                            text-align: center;
                        '>
                            Chez KAYART, chaque demande est traitée comme un projet unique,<br>
                            pensé pour la performance et façonné à la main.
                        </p>
                    </div>

                    <!-- TITRE PRINCIPAL -->
                    <div style='padding: 35px 35px 25px 35px;'>
                        <h2 style='
                            color: {$colorPrimary};
                            font-family: \"Georgia\", serif;
                            font-size: 24px;
                            margin: 0 0 10px 0;
                            font-weight: 700;
                            border-bottom: 2px solid {$colorAccent};
                            padding-bottom: 12px;
                            display: inline-block;
                        '>{$title}</h2>
                    </div>

                    <!-- CONTENU PRINCIPAL -->
                    <div style='
                        padding: 0 35px 30px 35px;
                        color: {$colorText};
                        font-size: 16px;
                        line-height: 1.7;
                    '>
                        {$content}
                    </div>

                    <!-- CALL TO ACTION -->
                    {$buttonHtml}

                    <!-- MICRO-SIGNATURE ARTISANALE -->
                    <div style='
                        background: linear-gradient(to right, {$bgAccent} 0%, {$bgContent} 50%, {$bgAccent} 100%);
                        padding: 25px 35px;
                        border-top: 1px solid {$borderSubtle};
                        text-align: center;
                    '>
                        <p style='
                            margin: 0 0 8px 0;
                            color: {$colorPrimary};
                            font-size: 16px;
                            font-weight: 600;
                        '>L'équipe KAYART</p>
                        <p style='
                            margin: 0;
                            color: {$colorAccent};
                            font-size: 13px;
                            font-weight: 500;
                            letter-spacing: 1px;
                        '>Composite carbone sur-mesure – Fabrication artisanale</p>
                    </div>

                    <!-- FOOTER MARQUE -->
                    <div style='
                        background-color: {$colorPrimary};
                        padding: 20px 35px;
                        text-align: center;
                        border-radius: 0 0 8px 8px;
                    '>
                        <p style='
                            margin: 0 0 12px 0;
                            font-size: 12px;
                            color: rgba(255, 255, 255, 0.7);
                        '>
                            <a href='" . site_url() . "' style='color: {$colorAccent}; text-decoration: none; margin: 0 8px;'>Site Web</a>
                            <span style='color: rgba(255, 255, 255, 0.3);'>•</span>
                            <a href='" . site_url('produits') . "' style='color: {$colorAccent}; text-decoration: none; margin: 0 8px;'>Nos Réalisations</a>
                            <span style='color: rgba(255, 255, 255, 0.3);'>•</span>
                            <a href='" . site_url('contact') . "' style='color: {$colorAccent}; text-decoration: none; margin: 0 8px;'>Contact</a>
                        </p>
                        <p style='
                            margin: 0;
                            font-size: 11px;
                            color: rgba(255, 255, 255, 0.5);
                        '>&copy; " . date('Y') . " KAYART – Tous droits réservés</p>
                    </div>

                </div>

            </div>
        </body>
        </html>
        ";
    }
}
