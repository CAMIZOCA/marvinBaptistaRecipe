<?php
/**
 * Seeds legal page content: Privacidad, Cookies, Aviso Legal.
 * Also fixes slug mismatch between footer links and DB.
 *
 * Run: php artisan tinker --execute="require database_path('scripts/seed_legal_pages.php');"
 */

$pages = [

    /* ── POLÍTICA DE PRIVACIDAD ─────────────────────────────────── */
    'privacidad' => [
        'title'           => 'Política de Privacidad',
        'old_slug'        => 'politica-de-privacidad',
        'seo_title'       => 'Política de Privacidad · marvinbaptista.com',
        'seo_description' => 'Conoce cómo marvinbaptista.com recopila, usa y protege tu información personal de acuerdo con el RGPD y la LOPD.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">En <strong>marvinbaptista.com</strong> nos tomamos muy en serio la privacidad de nuestros visitantes. Esta política explica qué información recopilamos, cómo la usamos y cuáles son tus derechos.</p>

  <h2>1. Responsable del tratamiento</h2>
  <p>
    <strong>Titular:</strong> Marvin Baptista<br>
    <strong>Sitio web:</strong> marvinbaptista.com<br>
    <strong>Contacto:</strong> hola@marvinbaptista.com
  </p>

  <h2>2. Datos que recopilamos</h2>
  <h3>2.1 Datos de navegación</h3>
  <p>Cuando visitas el sitio, nuestros servidores registran automáticamente información técnica como tu dirección IP (anonimizada), tipo de navegador, páginas visitadas y duración de la visita. Esta información se usa únicamente con fines estadísticos y de mejora del servicio.</p>

  <h3>2.2 Formulario de contacto / Newsletter</h3>
  <p>Si te suscribes a nuestro boletín o nos envías un mensaje, recopilamos tu dirección de correo electrónico y, opcionalmente, tu nombre. Estos datos se usan exclusivamente para enviarte contenido relacionado con recetas y novedades del blog.</p>

  <h3>2.3 Cookies</h3>
  <p>Utilizamos cookies propias y de terceros. Consulta nuestra <a href="/pagina/cookies">Política de Cookies</a> para más información.</p>

  <h2>3. Base legal del tratamiento</h2>
  <ul>
    <li><strong>Cookies técnicas:</strong> interés legítimo (funcionamiento del sitio).</li>
    <li><strong>Cookies analíticas y de terceros:</strong> consentimiento del usuario.</li>
    <li><strong>Newsletter:</strong> consentimiento expreso al suscribirse.</li>
  </ul>

  <h2>4. Terceros y transferencias internacionales</h2>
  <p>Podemos compartir datos estadísticos anónimos con los siguientes proveedores:</p>
  <ul>
    <li><strong>Google Analytics:</strong> análisis de tráfico (datos anonimizados). Política de privacidad de Google: <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">policies.google.com/privacy</a>.</li>
    <li><strong>Amazon Associates:</strong> programa de afiliados. Al hacer clic en enlaces de Amazon, Amazon puede depositar cookies en tu dispositivo conforme a su propia política de privacidad.</li>
  </ul>
  <p>No vendemos ni cedemos tus datos personales a terceros con fines comerciales.</p>

  <h2>5. Plazo de conservación</h2>
  <p>Los datos de suscripción se conservan mientras mantengas activa tu suscripción. Puedes darte de baja en cualquier momento. Los datos de navegación anonimizados se conservan un máximo de 26 meses.</p>

  <h2>6. Tus derechos</h2>
  <p>En virtud del RGPD (UE 2016/679) y la LOPD-GDD, tienes derecho a:</p>
  <ul>
    <li><strong>Acceso:</strong> conocer qué datos tenemos sobre ti.</li>
    <li><strong>Rectificación:</strong> corregir datos inexactos.</li>
    <li><strong>Supresión:</strong> solicitar la eliminación de tus datos.</li>
    <li><strong>Oposición y limitación:</strong> oponerte o limitar ciertos tratamientos.</li>
    <li><strong>Portabilidad:</strong> recibir tus datos en formato estructurado.</li>
  </ul>
  <p>Para ejercer cualquiera de estos derechos, escríbenos a <strong>hola@marvinbaptista.com</strong>. También puedes presentar una reclamación ante la Agencia Española de Protección de Datos (AEPD) en <a href="https://www.aepd.es" target="_blank" rel="noopener">www.aepd.es</a>.</p>

  <h2>7. Cambios en esta política</h2>
  <p>Nos reservamos el derecho de actualizar esta política. Te notificaremos de cambios significativos mediante un aviso visible en el sitio web o por correo electrónico si estás suscrito.</p>

  <p><em>Última actualización: marzo de 2025.</em></p>
</div>
HTML,
    ],

    /* ── POLÍTICA DE COOKIES ────────────────────────────────────── */
    'cookies' => [
        'title'           => 'Política de Cookies',
        'old_slug'        => 'politica-de-cookies',
        'seo_title'       => 'Política de Cookies · marvinbaptista.com',
        'seo_description' => 'Información sobre las cookies utilizadas en marvinbaptista.com, su finalidad y cómo gestionarlas.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">Las cookies son pequeños archivos de texto que los sitios web almacenan en tu dispositivo para recordar preferencias y recopilar información de uso. Esta política detalla cómo usamos las cookies en <strong>marvinbaptista.com</strong>.</p>

  <h2>1. Tipos de cookies que utilizamos</h2>

  <h3>1.1 Cookies técnicas (necesarias)</h3>
  <p>Son imprescindibles para el funcionamiento del sitio. Sin ellas, servicios como el inicio de sesión o la navegación segura no funcionarían correctamente. No requieren tu consentimiento.</p>
  <table>
    <thead><tr><th>Cookie</th><th>Finalidad</th><th>Duración</th></tr></thead>
    <tbody>
      <tr><td>marvin-baptista-session</td><td>Gestión de sesión de usuario</td><td>Sesión</td></tr>
      <tr><td>XSRF-TOKEN</td><td>Protección contra ataques CSRF</td><td>Sesión</td></tr>
    </tbody>
  </table>

  <h3>1.2 Cookies analíticas</h3>
  <p>Nos ayudan a entender cómo interactúan los visitantes con el sitio (páginas más vistas, tiempo de visita, etc.). Los datos se recopilan de forma anónima y agregada.</p>
  <table>
    <thead><tr><th>Cookie</th><th>Proveedor</th><th>Finalidad</th><th>Duración</th></tr></thead>
    <tbody>
      <tr><td>_ga</td><td>Google Analytics</td><td>Distinguir usuarios únicos</td><td>2 años</td></tr>
      <tr><td>_ga_*</td><td>Google Analytics</td><td>Estado de sesión</td><td>2 años</td></tr>
      <tr><td>_gid</td><td>Google Analytics</td><td>Distinguir usuarios (24h)</td><td>24 horas</td></tr>
    </tbody>
  </table>

  <h3>1.3 Cookies de terceros (marketing / afiliados)</h3>
  <p>Amazon puede depositar cookies cuando haces clic en los enlaces de afiliado presentes en nuestras recetas. Estas cookies permiten a Amazon rastrear las ventas referidas y son responsabilidad de Amazon.</p>
  <table>
    <thead><tr><th>Cookie</th><th>Proveedor</th><th>Finalidad</th><th>Duración</th></tr></thead>
    <tbody>
      <tr><td>session-id, ubid-main, etc.</td><td>Amazon Associates</td><td>Seguimiento de afiliados</td><td>Hasta 30 días</td></tr>
    </tbody>
  </table>

  <h2>2. Cómo gestionar las cookies</h2>
  <p>Puedes controlar y eliminar las cookies desde la configuración de tu navegador:</p>
  <ul>
    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Google Chrome</a></li>
    <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="noopener">Mozilla Firefox</a></li>
    <li><a href="https://support.microsoft.com/es-es/windows/eliminar-y-administrar-cookies-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener">Microsoft Edge</a></li>
    <li><a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a></li>
  </ul>
  <p>Ten en cuenta que bloquear ciertas cookies puede afectar la funcionalidad del sitio.</p>

  <h2>3. Base legal</h2>
  <p>Las cookies técnicas se basan en el interés legítimo (necesarias para el funcionamiento del servicio). Las cookies analíticas y de terceros se instalan solo si das tu consentimiento mediante el banner de cookies.</p>

  <h2>4. Más información</h2>
  <p>Para más detalles sobre el tratamiento de tus datos, consulta nuestra <a href="/pagina/privacidad">Política de Privacidad</a> o escríbenos a <strong>hola@marvinbaptista.com</strong>.</p>

  <p><em>Última actualización: marzo de 2025.</em></p>
</div>
HTML,
    ],

    /* ── AVISO LEGAL ────────────────────────────────────────────── */
    'aviso-legal' => [
        'title'           => 'Aviso Legal',
        'old_slug'        => 'aviso-legal',
        'seo_title'       => 'Aviso Legal · marvinbaptista.com',
        'seo_description' => 'Aviso legal, condiciones de uso y propiedad intelectual de marvinbaptista.com.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">El presente Aviso Legal regula el uso del sitio web <strong>marvinbaptista.com</strong> (en adelante, "el Sitio"), del que es titular Marvin Baptista.</p>

  <h2>1. Datos identificativos del titular</h2>
  <p>
    <strong>Nombre:</strong> Marvin Baptista<br>
    <strong>Sitio web:</strong> marvinbaptista.com<br>
    <strong>Correo de contacto:</strong> hola@marvinbaptista.com<br>
    <strong>Actividad:</strong> Blog de recetas de cocina y contenido gastronómico
  </p>

  <h2>2. Condiciones de uso</h2>
  <p>El acceso y uso del Sitio implica la aceptación plena de las presentes condiciones. Si no estás de acuerdo con alguna de ellas, debes abstenerte de usar el Sitio.</p>
  <p>El usuario se compromete a hacer un uso lícito del Sitio, sin vulnerar los derechos de terceros ni infringir la legislación vigente. Queda prohibido:</p>
  <ul>
    <li>Reproducir, copiar o distribuir el contenido del Sitio con fines comerciales sin autorización escrita.</li>
    <li>Utilizar técnicas de scraping o extracción automatizada de contenido.</li>
    <li>Introducir virus informáticos o cualquier código malicioso.</li>
  </ul>

  <h2>3. Propiedad intelectual e industrial</h2>
  <p>Todos los contenidos del Sitio (textos, fotografías, imágenes, recetas, diseño gráfico, código fuente, logotipos y marcas) son propiedad de Marvin Baptista o de terceros que han autorizado su uso, y están protegidos por la legislación española e internacional sobre propiedad intelectual e industrial.</p>
  <p>Se permite compartir y enlazar recetas individuales siempre que se cite la fuente con un enlace activo a <strong>marvinbaptista.com</strong>. No se autoriza la reproducción completa o parcial sin permiso expreso.</p>

  <h2>4. Programa de afiliados de Amazon</h2>
  <p>marvinbaptista.com participa en el Programa de Afiliados de Amazon EU, un programa de publicidad para afiliados diseñado para ofrecer a sitios web un modo de obtener comisiones por publicidad mediante la creación de enlaces a Amazon.es, Amazon.com, Amazon.com.mx y otras plataformas de Amazon.</p>
  <p><strong>Como Asociado de Amazon, obtenemos ingresos por las compras adscritas que cumplen los requisitos aplicables.</strong> El precio que tú pagas es el mismo; la comisión la paga Amazon al afiliado sin coste adicional para el comprador.</p>
  <p>Los libros y productos recomendados en este sitio son seleccionados por su calidad y relevancia gastronómica, independientemente de la comisión que generen.</p>

  <h2>5. Exclusión de responsabilidad</h2>
  <h3>5.1 Contenido del Sitio</h3>
  <p>Las recetas y consejos publicados tienen fines informativos. marvinbaptista.com no se hace responsable de los resultados obtenidos al seguir las recetas, posibles alergias alimentarias u otras consecuencias derivadas del uso de la información publicada. Consulta siempre a un especialista ante dudas sobre alergias o condiciones médicas relacionadas con la alimentación.</p>
  <h3>5.2 Enlaces externos</h3>
  <p>El Sitio puede contener enlaces a sitios de terceros. marvinbaptista.com no controla ni se responsabiliza del contenido de dichos sitios ni de su política de privacidad.</p>
  <h3>5.3 Disponibilidad del servicio</h3>
  <p>El titular no garantiza la disponibilidad ininterrumpida del Sitio y se reserva el derecho de modificar, suspender o interrumpir el acceso sin previo aviso por razones técnicas o de mantenimiento.</p>

  <h2>6. Legislación aplicable y jurisdicción</h2>
  <p>Este Aviso Legal se rige por la legislación española. Para cualquier controversia derivada del uso del Sitio, las partes se someten, con renuncia expresa a cualquier otro fuero, a los Juzgados y Tribunales del domicilio del usuario, en cumplimiento de la normativa vigente de protección de consumidores.</p>

  <h2>7. Modificaciones</h2>
  <p>El titular se reserva el derecho de actualizar, modificar o eliminar la información contenida en este Aviso Legal, sin que exista obligación de preavisar a los usuarios.</p>

  <p><em>Última actualización: marzo de 2025.</em></p>
</div>
HTML,
    ],

];

// ── Update DB ──────────────────────────────────────────────────────────────────

$updated = 0;
foreach ($pages as $newSlug => $data) {
    // Try by new slug first, then by old slug
    $page = \App\Models\Page::where('slug', $newSlug)->first()
         ?? \App\Models\Page::where('slug', $data['old_slug'])->first();

    if (!$page) {
        // Create it
        $page = new \App\Models\Page();
        echo "  [CREATE] {$data['title']}\n";
    } else {
        echo "  [UPDATE] {$data['title']} (slug: {$page->slug} → {$newSlug})\n";
    }

    $page->slug            = $newSlug;
    $page->title           = $data['title'];
    $page->content         = $data['content'];
    $page->seo_title       = $data['seo_title'];
    $page->seo_description = $data['seo_description'];
    $page->is_published    = true;
    $page->save();
    $updated++;
}

echo "\nDone. {$updated} page(s) updated.\n";
