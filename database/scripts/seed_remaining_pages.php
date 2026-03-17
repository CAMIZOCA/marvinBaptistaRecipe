<?php
/**
 * Pobla con contenido real las páginas: Términos y Condiciones, Sobre Mí, Contacto.
 */

$pages = [

    /* ── TÉRMINOS Y CONDICIONES ─────────────────────────────────── */
    'terminos-y-condiciones' => [
        'title'           => 'Términos y Condiciones',
        'seo_title'       => 'Términos y Condiciones · marvinbaptista.com',
        'seo_description' => 'Términos y condiciones de uso del sitio web marvinbaptista.com.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">Al acceder y utilizar <strong>marvinbaptista.com</strong>, aceptas los siguientes términos y condiciones de uso. Si no estás de acuerdo, por favor abstente de usar el sitio.</p>

  <h2>1. Objeto y ámbito de aplicación</h2>
  <p>Estos términos regulan el acceso y uso del sitio web marvinbaptista.com, un blog de recetas de cocina latinoamericana y mediterránea gestionado por Marvin Baptista.</p>

  <h2>2. Uso del contenido</h2>
  <p>Todo el contenido publicado en este sitio (recetas, textos, fotografías, vídeos y diseño) está protegido por derechos de autor. Se permite:</p>
  <ul>
    <li>Consultar y utilizar las recetas para uso personal y doméstico.</li>
    <li>Compartir un enlace a cualquier página del sitio en redes sociales o blogs, citando la fuente.</li>
    <li>Imprimir recetas para uso personal sin distribución comercial.</li>
  </ul>
  <p>Queda <strong>prohibido</strong> sin autorización escrita previa:</p>
  <ul>
    <li>Reproducir o publicar el contenido en otros sitios web.</li>
    <li>Utilizar el contenido con fines comerciales.</li>
    <li>Modificar, adaptar o crear obras derivadas para distribución pública.</li>
  </ul>

  <h2>3. Exactitud de la información</h2>
  <p>Las recetas y consejos publicados se elaboran con el máximo cuidado. Sin embargo, los tiempos de cocción, cantidades y resultados pueden variar según el equipamiento, altitud, marca de ingredientes y otros factores. marvinbaptista.com no garantiza resultados específicos y no se responsabiliza de posibles errores tipográficos u omisiones.</p>

  <h2>4. Información nutricional</h2>
  <p>Los datos nutricionales (cuando se proporcionan) son estimaciones aproximadas calculadas con herramientas digitales. No deben utilizarse como guía médica o dietética definitiva. Consulta a un profesional de la salud para asesoramiento nutricional personalizado.</p>

  <h2>5. Alérgenos y restricciones alimentarias</h2>
  <p>Las recetas pueden contener o estar en contacto con alérgenos comunes. Siempre revisa los ingredientes si tienes alergias o intolerancias alimentarias. marvinbaptista.com no se hace responsable de reacciones alérgicas derivadas del uso de las recetas publicadas.</p>

  <h2>6. Contenido de terceros y enlaces externos</h2>
  <p>El sitio puede incluir enlaces a recursos externos (YouTube, Amazon, otros blogs). marvinbaptista.com no controla ni respalda el contenido de dichos sitios y no se responsabiliza de su disponibilidad o contenido.</p>

  <h2>7. Programa de afiliados</h2>
  <p>Algunos enlaces en el sitio son enlaces de afiliado (principalmente Amazon Associates). Si realizas una compra a través de ellos, podría recibir una pequeña comisión sin coste adicional para ti. Solo recomiendo productos que he revisado personalmente o que considero de calidad.</p>

  <h2>8. Comentarios y comunidad</h2>
  <p>Si en el futuro el sitio incorpora sección de comentarios, los usuarios son responsables de su contenido. Queda prohibido publicar contenido ofensivo, spam, material ilegal o que infrinja derechos de terceros. marvinbaptista.com se reserva el derecho de moderar o eliminar comentarios.</p>

  <h2>9. Modificaciones</h2>
  <p>Estos términos pueden actualizarse en cualquier momento. El uso continuado del sitio tras la publicación de cambios implica la aceptación de los nuevos términos.</p>

  <h2>10. Legislación aplicable</h2>
  <p>Estos términos se rigen por la legislación española. Cualquier disputa se someterá a los tribunales competentes según la normativa de protección al consumidor vigente.</p>

  <p><em>Última actualización: marzo de 2025.</em></p>
</div>
HTML,
    ],

    /* ── SOBRE MÍ ────────────────────────────────────────────────── */
    'sobre-mi' => [
        'title'           => 'Sobre Mí',
        'seo_title'       => 'Sobre Mí · Marvin Baptista — Chef y escritor gastronómico',
        'seo_description' => 'Conoce a Marvin Baptista, chef apasionado por la cocina latinoamericana y mediterránea y el creador de marvinbaptista.com.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">Hola, soy <strong>Marvin Baptista</strong>, un apasionado de la cocina latinoamericana y mediterránea. Este espacio nació de mi amor por las recetas con historia, los ingredientes auténticos y el placer de cocinar en casa.</p>

  <h2>Mi historia</h2>
  <p>Crecí rodeado de los aromas de la cocina ecuatoriana: el seco de pollo de los domingos, el ceviche de los viernes, los bolones de madrugada. Esos sabores me formaron y me enseñaron que la comida es mucho más que nutrición: es memoria, identidad y comunidad.</p>
  <p>Con los años amplié mi horizonte culinario hacia la cocina mediterránea, fascinado por su filosofía de ingredientes simples, frescos y de temporada. Italia, España y el Medio Oriente me regalaron nuevas técnicas y combinaciones que hoy conviven en mi cocina con las recetas de mi tierra.</p>

  <h2>Por qué este blog</h2>
  <p>Empecé marvinbaptista.com porque echaba de menos encontrar recetas latinoamericanas bien documentadas: con tiempos precisos, trucos de chef, variaciones regionales e historia detrás de cada plato. No solo instrucciones, sino contexto.</p>
  <p>Cada receta que publico la preparo, la pruebo, la ajusto y la fotografío yo mismo. Si algo no funciona en mi cocina, no llega al blog.</p>

  <h2>Qué encontrarás aquí</h2>
  <ul>
    <li><strong>Recetas con alma:</strong> del seco al risotto, del ceviche a la pasta alla norma.</li>
    <li><strong>Técnicas explicadas:</strong> sin dar nada por sentado, pensando en el cocinero de casa.</li>
    <li><strong>Historia gastronómica:</strong> el origen de cada plato y su evolución.</li>
    <li><strong>Ingredientes auténticos:</strong> dónde encontrarlos y cómo sustituirlos.</li>
    <li><strong>Libros recomendados:</strong> mi selección personal de los mejores libros de cocina.</li>
  </ul>

  <h2>¿Hablamos?</h2>
  <p>Me encanta saber qué recetas probaste, qué te salió fenomenal y qué preguntas tienes. Escríbeme a <a href="mailto:hola@marvinbaptista.com">hola@marvinbaptista.com</a> o encuéntrame en Instagram como <a href="https://instagram.com/marvinbaptista" target="_blank" rel="noopener">@marvinbaptista</a>.</p>
  <p>¡Buen provecho! 🍽️</p>
</div>
HTML,
    ],

    /* ── CONTACTO ────────────────────────────────────────────────── */
    'contacto' => [
        'title'           => 'Contacto',
        'seo_title'       => 'Contacto · marvinbaptista.com',
        'seo_description' => 'Contacta con Marvin Baptista para colaboraciones, preguntas sobre recetas o cualquier consulta.',
        'content'         => <<<HTML
<div class="prose-section">
  <p class="lead">¿Tienes una pregunta sobre una receta, una sugerencia o quieres colaborar? Estaré encantado de escucharte.</p>

  <h2>📧 Correo electrónico</h2>
  <p>La forma más rápida de contactarme es por correo:<br>
  <a href="mailto:hola@marvinbaptista.com" class="font-semibold text-amber-600 hover:text-amber-700">hola@marvinbaptista.com</a></p>
  <p>Intento responder en un plazo de 48–72 horas laborables.</p>

  <h2>📱 Redes sociales</h2>
  <p>También puedes escribirme directamente en mis redes sociales, donde comparto recetas, trucos y el día a día de mi cocina:</p>
  <ul>
    <li><strong>Instagram:</strong> <a href="https://instagram.com/marvinbaptista" target="_blank" rel="noopener">@marvinbaptista</a></li>
    <li><strong>YouTube:</strong> Canal Marvin Baptista</li>
    <li><strong>Pinterest:</strong> marvinbaptista</li>
  </ul>

  <h2>🤝 Colaboraciones</h2>
  <p>Estoy abierto a colaboraciones con marcas de alimentación, utensilios de cocina y productos gastronómicos que compartan mis valores de calidad y autenticidad. Para propuestas de colaboración, por favor incluye en tu correo:</p>
  <ul>
    <li>Descripción de tu marca y producto</li>
    <li>Tipo de colaboración propuesta</li>
    <li>Plazos previstos</li>
  </ul>
  <p><em>Solo acepto colaboraciones con productos que pueda recomendar honestamente.</em></p>

  <h2>📚 Libros y recetas específicas</h2>
  <p>Si buscas una receta concreta que no encuentras en el blog, o tienes dudas sobre alguna preparación, escríbeme. No prometo publicar todo, ¡pero me sirve de inspiración!</p>
</div>
HTML,
    ],

];

// ── Update DB ──────────────────────────────────────────────────────────────────

$updated = 0;
foreach ($pages as $slug => $data) {
    $page = \App\Models\Page::where('slug', $slug)->first();

    if (!$page) {
        $page = new \App\Models\Page();
        echo "  [CREATE] {$data['title']}" . PHP_EOL;
    } else {
        echo "  [UPDATE] {$data['title']} (slug: {$slug})" . PHP_EOL;
    }

    $page->slug            = $slug;
    $page->title           = $data['title'];
    $page->content         = $data['content'];
    $page->seo_title       = $data['seo_title'];
    $page->seo_description = $data['seo_description'];
    $page->is_published    = true;
    $page->save();
    $updated++;
}

echo PHP_EOL . "Done. {$updated} page(s) updated." . PHP_EOL;
