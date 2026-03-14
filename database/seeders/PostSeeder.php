<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [

            /* ─── 1 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Los 10 Ingredientes Esenciales de la Cocina Latinoamericana',
                'slug'     => 'ingredientes-esenciales-cocina-latinoamericana',
                'category' => 'Ingredientes',
                'excerpt'  => 'Descubre los ingredientes que no pueden faltar en tu despensa si quieres cocinar auténtica gastronomía latinoamericana: desde el achiote hasta el ají amarillo.',
                'featured_image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&q=80',
                'image_alt' => 'Especias y condimentos latinoamericanos sobre madera',
                'content'  => '<h2>La despensa latinoamericana: un universo de sabor</h2>
<p>La cocina latinoamericana es una de las más ricas y diversas del mundo. Abarca desde el altiplano andino hasta el Caribe, pasando por la selva amazónica y las costas del Pacífico. Cada región aporta ingredientes únicos que, juntos, forman un mosaico de sabores inigualable.</p>
<p>Si quieres adentrarte en esta gastronomía y preparar recetas auténticas en casa, hay diez ingredientes que debes tener siempre a mano.</p>

<h2>1. Achiote (Bixa orellana)</h2>
<p>Conocido también como <em>annatto</em> o <em>onoto</em>, el achiote es el colorante natural más importante de la cocina latinoamericana. Sus semillas de color rojo intenso tiñen los guisos, arroces y marinadas con una tonalidad dorada inconfundible. Más allá del color, aporta un sabor terroso y ligeramente pimienta que es la base del sazón ecuatoriano, colombiano y venezolano.</p>
<p><strong>Cómo usarlo:</strong> Infusiona las semillas en aceite caliente durante 3 minutos a fuego bajo. Retíralas y usa ese aceite para sofritosyeguisos.</p>

<h2>2. Ají Amarillo Peruano</h2>
<p>El ají amarillo es el alma de la cocina peruana. Con un picor moderado y un aroma frutal único, este chile es indispensable para el ceviche, el ají de gallina y las cremas de la gastronomía limeña. No tiene sustituto real: su sabor es completamente único en el mundo.</p>
<p>Puedes encontrarlo fresco, congelado o en pasta. La pasta es la opción más práctica para cocinar en casa.</p>

<h2>3. Cilantro fresco</h2>
<p>Amado u odiado, el cilantro es un pilar de la cocina latinoamericana. Se usa fresco, nunca seco, como finishing herb en sopas, ceviches, salsas y guisos. Tiene propiedades antioxidantes y aporta frescura a cualquier plato.</p>

<h2>4. Plátano verde y maduro</h2>
<p>El plátano —en sus diferentes estados de maduración— es un ingrediente versátil que aparece en toda América Latina. Verde, se fríe para hacer tostones o patacones; maduro, se carameliza o se usa en el madurito frito. En Ecuador, el verde rallado es la base de muchos encebollados y sopas.</p>

<h2>5. Comino</h2>
<p>El comino es la especia más utilizada en Latinoamérica, presente en guisos de carne, frijoles, arroces y marinadas. Se usa molido y en pequeñas cantidades: su aroma es potente y puede dominar el plato si se excede.</p>

<h2>6. Ajo y cebolla blanca</h2>
<p>La base de casi todo sofrito latinoamericano es ajo y cebolla. En Ecuador se usa específicamente la cebolla blanca o paiteña; en México, la cebolla blanca dulce; en el Caribe, a veces se combina con ají cubanelle. No hay guiso, sopa o estofado que no empiece con estos dos ingredientes.</p>

<h2>7. Tomate riñón</h2>
<p>El tomate fresco es el tercer elemento del sofrito base. En Colombia y Ecuador se usa el tomate riñón, de piel gruesa y sabor ácido, ideal para guisar a fuego lento. Rallado directamente en el refrito, se integra perfectamente con los demás ingredientes.</p>

<h2>8. Hierba luisa y hoja de naranja</h2>
<p>Estas hierbas aromáticas son el secreto de los caldos y sopas andinas. La hierba luisa aporta un aroma cítrico delicado; la hoja de naranja agria da profundidad a estofados y menestras. Son difíciles de encontrar fuera de la región, pero valen la pena buscarlas en mercados latinoamericanos.</p>

<h2>9. Maíz en todas sus formas</h2>
<p>El maíz es el grano sagrado de América. Seco, molido, fermentado, tostado o tierno: cada preparación produce un alimento distinto. La harina de maíz precocida (masa lista) es indispensable para arepas y tamales; el mote o choclo cocido aparece en sopas y ceviches.</p>

<h2>10. Limón sutil (lima peruana)</h2>
<p>No confundas el limón sutil peruano con el limón amarillo europeo. El limón sutil es pequeño, verde, con un aroma floral intenso y altísima acidez. Es el ingrediente clave del ceviche, la causa y muchas salsas peruanas. Si no consigues limón sutil, usa lima Key o una mezcla de limón y lima.</p>

<h2>Conclusión</h2>
<p>Con estos diez ingredientes en tu despensa, puedes preparar cientos de recetas latinoamericanas con autenticidad. Cada uno aporta algo insustituible: color, aroma, textura o acidez. La cocina latinoamericana es generosa y expresiva, y estos ingredientes son la clave para entenderla.</p>',
                'seo_title'       => 'Los 10 Ingredientes Esenciales de la Cocina Latinoamericana',
                'seo_description' => 'Descubre los ingredientes que no pueden faltar en tu despensa para cocinar auténtica gastronomía latinoamericana, desde achiote hasta ají amarillo.',
                'published_at'    => Carbon::now()->subDays(2),
            ],

            /* ─── 2 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Historia del Ceviche: El Plato Más Icónico del Pacífico',
                'slug'     => 'historia-del-ceviche-plato-iconico-pacifico',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'El ceviche tiene más de 2.000 años de historia. Desde las costas de los moches hasta las mesas de los mejores restaurantes del mundo, este es el relato de un plato inmortal.',
                'featured_image' => 'https://images.unsplash.com/photo-1535400255456-984b28c59eca?w=800&q=80',
                'image_alt' => 'Ceviche de mariscos fresco con limón y cilantro',
                'content'  => '<h2>Los orígenes precolombinos</h2>
<p>El ceviche es mucho más que una receta: es un documento histórico comestible. Las primeras evidencias de marinado de pescado crudo con ácidos cítricos en la costa peruana datan de hace más de 2.000 años, cuando la civilización moche preparaba un plato llamado <em>siwichi</em> con pescado macerado en jugo de tumbo, una fruta local de alta acidez.</p>
<p>Los moches fueron una sociedad costera avanzada que dominó la pesca y el comercio marítimo entre los años 100 a.C. y 700 d.C. Para ellos, el pescado marinado era tanto alimento cotidiano como ofrenda ceremonial.</p>

<h2>La llegada del limón: el giro que cambió todo</h2>
<p>Cuando los conquistadores españoles llegaron a las costas del Pacífico en el siglo XVI, trajeron consigo el limón árabe, introducido previamente en España por los moros. Este cítrico, de acidez mucho más intensa que el tumbo original, transformó por completo el marinado.</p>
<p>El encuentro entre la técnica indígena del marinado y el limón español produjo una reacción química perfecta: las proteínas del pescado se desnaturalizan con el ácido cítrico en un proceso llamado <em>cocción en frío</em>, que da al ceviche su textura característica.</p>

<h2>La influencia africana y japonesa</h2>
<p>El ceviche no termina de formarse hasta el siglo XIX, cuando dos influencias externas lo remodelan. Las poblaciones africanas esclavizadas traídas al virreinato del Perú aportaron la costumbre de añadir ají y hierbas aromáticas con generosidad. Más tarde, la inmigración japonesa a finales del siglo XIX introdujo el concepto del corte preciso del pescado y los tiempos de marinado ultra cortos —el llamado <em>ceviche a la minuta</em>— que hoy es el estándar de la alta cocina peruana.</p>

<h2>El boom gastronómico peruano</h2>
<p>En las últimas dos décadas, el ceviche ha alcanzado reconocimiento internacional gracias al boom de la gastronomía peruana liderado por chefs como Gastón Acurio. En 2004, el gobierno peruano declaró el ceviche Patrimonio Cultural de la Nación, y en 2023 fue inscrito en la lista de Patrimonio Cultural Inmaterial de la Humanidad de la UNESCO.</p>

<h2>El ceviche en el resto de Latinoamérica</h2>
<p>Aunque Perú es su hogar original, el ceviche tiene variantes riquísimas en toda América Latina. En Ecuador se prepara con camarón y tomate, con una base más dulce que el peruano. En México predomina el ceviche de camarón con clamato, tomate y aguacate. En Colombia el ceviche de camarón se sirve con galletas de soda. Cada versión refleja los ingredientes locales y el carácter de cada cultura.</p>

<h2>La ciencia detrás del plato</h2>
<p>El ácido cítrico del limón desnaturaliza las proteínas del pescado de forma similar a como lo hace el calor. Las cadenas proteicas se despliegan y se vuelven opacas, dando esa textura firme característica. Sin embargo, a diferencia de la cocción por calor, este proceso no elimina todos los patógenos, razón por la que la calidad del pescado es crítica: siempre debe ser fresco o previamente congelado.</p>

<h2>Conclusión</h2>
<p>El ceviche es la prueba de que los mejores platos no se inventan: se construyen durante siglos a través de encuentros culturales, ingredientes viajeros y manos que transmiten el conocimiento. Cada bocado es historia, ciencia y placer reunidos en un cuenco.</p>',
                'seo_title'       => 'Historia del Ceviche: 2.000 Años del Plato Más Icónico del Pacífico',
                'seo_description' => 'Descubre la historia del ceviche: desde los moches precolombinos hasta el Patrimonio UNESCO. Orígenes, evolución y ciencia del plato más famoso de América.',
                'published_at'    => Carbon::now()->subDays(5),
            ],

            /* ─── 3 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Cómo Hacer un Sofrito Perfecto: La Base de 100 Recetas',
                'slug'     => 'como-hacer-sofrito-perfecto-base-recetas',
                'category' => 'Técnicas',
                'excerpt'  => 'El sofrito es la base aromática de cientos de platos latinoamericanos. Aprende a prepararlo correctamente: temperaturas, tiempos y el orden exacto de los ingredientes.',
                'featured_image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80',
                'image_alt' => 'Sofrito de cebolla, ajo y tomate en sartén',
                'content'  => '<h2>¿Qué es el sofrito?</h2>
<p>El sofrito —llamado también <em>refrito</em> en Ecuador, <em>hogao</em> en Colombia, <em>salsa criolla</em> en Argentina— es la base aromática que da profundidad a prácticamente toda la cocina latinoamericana. Es el paso inicial de sopas, arroces, guisos y estofados, y de su correcta ejecución depende en gran parte el resultado final del plato.</p>
<p>A diferencia del sofrito mediterráneo, que suele llevar solo ajo y aceite, el sofrito latinoamericano es más complejo: incluye cebolla, ajo, tomate, pimiento y una mezcla de especias que varía por región.</p>

<h2>El orden importa: la secuencia correcta</h2>
<p>Uno de los errores más comunes al hacer sofrito es añadir todos los ingredientes al mismo tiempo. Cada ingrediente tiene un tiempo de cocción distinto y necesita su espacio para liberar sus aromas correctamente.</p>
<p>El orden correcto es:</p>
<ol>
<li><strong>Aceite + achiote</strong> (si se usa): calentar el aceite a fuego medio e infusionar el achiote 2 minutos.</li>
<li><strong>Cebolla</strong>: añadir primero y cocinar 5-7 minutos hasta que esté transparente y ligeramente dorada.</li>
<li><strong>Ajo</strong>: añadir después de la cebolla y cocinar 1-2 minutos. El ajo se quema fácilmente y el quemado amarga todo el sofrito.</li>
<li><strong>Pimiento o ají</strong>: añadir junto con el ajo o justo después, cocinar 3-4 minutos.</li>
<li><strong>Tomate</strong>: rallado o en brunoise, añadir al final. Cocinar hasta que pierda el agua y se integre completamente, unos 8-10 minutos.</li>
<li><strong>Especias secas</strong> (comino, orégano): añadir junto con el tomate para que se "tuesten" ligeramente en la grasa.</li>
</ol>

<h2>La temperatura correcta</h2>
<p>El sofrito debe cocinarse a fuego medio, nunca alto. El calor excesivo quema los azúcares de la cebolla antes de que se caramelicen correctamente y puede amargar el ajo. La paciencia es la técnica más importante aquí.</p>
<p>Un sofrito bien hecho tarda entre 15 y 20 minutos. Si lo haces en 5 minutos, no has hecho un sofrito: has salteado vegetales crudos.</p>

<h2>La caramelización: el secreto del sabor profundo</h2>
<p>Los azúcares naturales de la cebolla comienzan a caramelizarse entre 160°C y 180°C. Este proceso, llamado reacción de Maillard, produce cientos de compuestos aromáticos nuevos que no existían en la cebolla cruda. Es la diferencia entre un guiso plano y uno con profundidad.</p>
<p>Sabrás que la cebolla está lista cuando haya reducido a la mitad de su volumen original, tenga un color dorado uniforme y huela dulce, no picante.</p>

<h2>Variaciones regionales</h2>
<ul>
<li><strong>Ecuador (refrito)</strong>: achiote, cebolla blanca, ajo, tomate, comino, cilantro.</li>
<li><strong>Colombia (hogao)</strong>: tomate y cebolla larga (cebolleta), sin ajo, cocinados hasta casi deshacerse.</li>
<li><strong>México (recaudo)</strong>: jitomate, cebolla, ajo, chile, a veces asados antes de moler.</li>
<li><strong>Cuba y Caribe</strong>: ajo, cebolla, pimiento verde y rojo, ají cubanelle, tomate, orégano.</li>
<li><strong>España</strong>: ajo, tomate rallado, aceite de oliva extra virgen, cocinado lentamente.</li>
</ul>

<h2>Consejos de chef</h2>
<p><strong>Haz sofrito en cantidad y congela.</strong> El sofrito se puede congelar en bandejas de hielo y usar directamente congelado en guisos. Ahorra tiempo enorme en el día a día sin sacrificar sabor.</p>
<p><strong>Usa aceite de calidad.</strong> El sofrito es mayoritariamente grasa: el sabor del aceite impacta directamente el resultado. En cocina latinoamericana se usa aceite vegetal neutro (girasol, canola); si quieres más sabor, mezcla con un chorrito de aceite de oliva.</p>',
                'seo_title'       => 'Cómo Hacer un Sofrito Perfecto: Técnica, Tiempos y Secretos',
                'seo_description' => 'Aprende a hacer el sofrito latinoamericano perfecto: el orden de los ingredientes, temperaturas y variaciones regionales de esta técnica fundamental.',
                'published_at'    => Carbon::now()->subDays(8),
            ],

            /* ─── 4 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'El Achiote: El Oro Rojo de la Cocina Ecuatoriana',
                'slug'     => 'achiote-oro-rojo-cocina-ecuatoriana',
                'category' => 'Ingredientes',
                'excerpt'  => 'El achiote es mucho más que un colorante. Descubre su historia milenaria, sus propiedades medicinales y cómo usarlo correctamente en la cocina ecuatoriana.',
                'featured_image' => 'https://images.unsplash.com/photo-1557844352-761f2565b576?w=800&q=80',
                'image_alt' => 'Semillas de achiote rojas sobre tabla de madera',
                'content'  => '<h2>Qué es el achiote y por qué es tan especial</h2>
<p>El achiote (<em>Bixa orellana</em>) es un arbusto tropical originario de América del Sur y el Caribe. Sus semillas, recubiertas de una pasta roja llamada <em>bixina</em>, han sido usadas por los pueblos indígenas americanos durante miles de años como pigmento corporal, medicina y condimento.</p>
<p>En la cocina ecuatoriana, el achiote es tan fundamental que prácticamente ningún plato tradicional puede prescindir de él. El encebollado, la sopa de pollo, el sancocho, el caldo de patas, las papas con cuero: todos llevan achiote en su refrito.</p>

<h2>Historia y usos tradicionales</h2>
<p>Las civilizaciones precolombinas de Ecuador, Colombia y Venezuela usaban el achiote (llamado <em>manduru</em> en quichua) principalmente como pigmento corporal y de guerra. Los indios caribes lo mezclaban con aceite de serpiente y grasa animal para pintar sus cuerpos y proteger su piel del sol y los insectos.</p>
<p>Los cronistas españoles del siglo XVI quedaron impresionados por las "pieles rojas" de los indígenas, lo que llevó al equívoco término "pieles rojas" para describir a todos los nativos americanos. El color era, en realidad, achiote.</p>
<p>Con el tiempo, los pueblos andinos incorporaron el achiote a la cocina como colorante natural y condimento, reemplazando parcialmente el azafrán europeo que era prohibitivamente caro.</p>

<h2>Propiedades nutricionales y medicinales</h2>
<p>La bixina del achiote es un carotenoide con propiedades antioxidantes demostradas. Estudios recientes sugieren que tiene efectos antiinflamatorios, protege la piel del daño ultravioleta y contribuye a regular el colesterol. En la medicina tradicional ecuatoriana, el achiote se usa para tratar quemaduras, dolores de garganta y como protector solar natural.</p>

<h2>Achiote en pasta vs. aceite de achiote vs. semillas</h2>
<p>El achiote se consigue en tres formas en el mercado:</p>
<ul>
<li><strong>Semillas secas</strong>: la forma más pura, se infusionan en aceite caliente para extraer el color y el sabor.</li>
<li><strong>Pasta de achiote</strong>: mezcla de semillas molidas con especias, vinagre y sal. Lista para usar directamente en marinadas y adobos.</li>
<li><strong>Aceite de achiote</strong>: aceite vegetal infusionado con semillas, listo para sofritosyrefrescos. Es la opción más práctica en cocina diaria.</li>
</ul>

<h2>Cómo hacer aceite de achiote en casa</h2>
<p>Es sencillo y dura semanas en el refrigerador:</p>
<ol>
<li>Calienta ½ taza de aceite vegetal neutro a fuego bajo.</li>
<li>Añade 2 cucharadas de semillas de achiote.</li>
<li>Cocina a fuego muy bajo 3-4 minutos. El aceite se teñirá de un rojo-naranja intenso.</li>
<li>Retira del fuego y deja enfriar 5 minutos.</li>
<li>Cuela las semillas y guarda el aceite en un frasco de vidrio.</li>
</ol>
<p>No uses fuego alto: el achiote se quema rápidamente y toma un sabor amargo desagradable.</p>

<h2>El achiote en la cocina internacional</h2>
<p>Fuera de América Latina, el achiote es conocido en la industria alimentaria como E-160b, donde se usa para colorear quesos (el cheddar naranja), mantequillas, margarinas y snacks. Es un colorante natural que reemplaza artificiales como el FD&C Yellow #6.</p>',
                'seo_title'       => 'El Achiote en la Cocina Ecuatoriana: Historia, Usos y Receta de Aceite',
                'seo_description' => 'Todo sobre el achiote: el ingrediente clave de la cocina ecuatoriana. Historia milenaria, propiedades medicinales y cómo hacer aceite de achiote en casa.',
                'published_at'    => Carbon::now()->subDays(12),
            ],

            /* ─── 5 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Cocina Mediterránea: Por Qué Es la Dieta Más Saludable del Mundo',
                'slug'     => 'cocina-mediterranea-dieta-mas-saludable-mundo',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'La dieta mediterránea lleva décadas en el top de las recomendaciones de nutricionistas y cardiólogos. Conoce sus principios, sus ingredientes clave y por qué funciona.',
                'featured_image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800&q=80',
                'image_alt' => 'Mesa mediterránea con aceite de oliva, verduras y pan',
                'content'  => '<h2>Qué es la dieta mediterránea</h2>
<p>La dieta mediterránea no es un régimen de adelgazamiento puntual: es un patrón alimentario completo que engloba los hábitos de vida de las poblaciones que bordean el mar Mediterráneo. Incluye no solo qué comer, sino cómo comer: en familia, con calma, disfrutando cada bocado.</p>
<p>En 2013, la UNESCO la incluyó en la Lista del Patrimonio Cultural Inmaterial de la Humanidad, reconociéndola como un patrimonio cultural además de nutricional.</p>

<h2>La pirámide mediterránea</h2>
<p>La base de la dieta mediterránea está compuesta por:</p>
<ul>
<li><strong>Aceite de oliva virgen extra</strong> como grasa principal</li>
<li><strong>Vegetales y frutas</strong> en abundancia (mínimo 5 raciones diarias)</li>
<li><strong>Legumbres</strong>: lentejas, garbanzos, alubias, al menos 3 veces por semana</li>
<li><strong>Cereales integrales</strong>: pan, pasta y arroz en versiones completas</li>
<li><strong>Frutos secos y semillas</strong> como snack principal</li>
<li><strong>Pescado y marisco</strong>: 2-3 veces por semana</li>
<li><strong>Carnes blancas</strong> (pollo, pavo): con moderación</li>
<li><strong>Lácteos fermentados</strong>: yogur y quesos curados ocasionalmente</li>
<li><strong>Carnes rojas y procesadas</strong>: raramente</li>
</ul>

<h2>La ciencia detrás de sus beneficios</h2>
<p>El Estudio PREDIMED (Prevención con Dieta Mediterránea), publicado en el <em>New England Journal of Medicine</em> en 2013, fue uno de los ensayos clínicos más grandes realizados sobre nutrición. Sus conclusiones fueron contundentes: seguir una dieta mediterránea suplementada con aceite de oliva virgen extra o frutos secos reducía el riesgo de eventos cardiovasculares mayores (infarto, ictus) en un 30% respecto a una dieta baja en grasas.</p>

<h2>El aceite de oliva: el ingrediente estrella</h2>
<p>El aceite de oliva virgen extra contiene hasta un 75% de ácido oleico, un ácido graso monoinsaturado que mejora el perfil lipídico en sangre. Pero su beneficio más estudiado proviene de sus polifenoles: compuestos antioxidantes que reducen la inflamación, protegen las células del envejecimiento y tienen potencial anticancerígeno.</p>
<p>Para obtener sus beneficios al máximo, úsalo en crudo: en ensaladas, sobre verduras asadas, en pan tostado. El calor degrada parcialmente sus polifenoles.</p>

<h2>La dieta mediterránea y la longevidad</h2>
<p>Las llamadas "Zonas Azules" —regiones donde la gente vive más de 100 años— incluyen Cerdeña (Italia) e Icaria (Grecia), ambas en el Mediterráneo. Estudios de estas poblaciones revelan que comer muchas legumbres, poco azúcar, aceite de oliva y compartir las comidas en familia son factores directamente correlacionados con la longevidad.</p>

<h2>Cómo incorporarla a tu vida cotidiana</h2>
<p>No hace falta vivir en Grecia para adoptar principios mediterráneos. Cambios simples con gran impacto:</p>
<ul>
<li>Sustituye la mantequilla por aceite de oliva en pan y cocina.</li>
<li>Cocina lentejas o garbanzos al menos 2 veces por semana.</li>
<li>Añade frutos secos (nueces, almendras) a tus desayunos.</li>
<li>Incorpora pescado dos veces por semana en lugar de carne roja.</li>
<li>Come siempre vegetales crudos como entrante.</li>
</ul>',
                'seo_title'       => 'Cocina Mediterránea: La Dieta Más Saludable del Mundo Explicada',
                'seo_description' => 'Por qué la dieta mediterránea es la más estudiada y recomendada. Sus ingredientes clave, la ciencia detrás de sus beneficios y cómo adoptarla en tu vida.',
                'published_at'    => Carbon::now()->subDays(15),
            ],

            /* ─── 6 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Técnicas de Cocción: Guía Completa para Cocinar Mejor',
                'slug'     => 'tecnicas-coccion-guia-completa-cocinar-mejor',
                'category' => 'Técnicas',
                'excerpt'  => 'Dominar las técnicas de cocción básicas transforma completamente tu forma de cocinar. Aprende la diferencia entre saltear, freír, estofar, asar y hervir, y cuándo usar cada una.',
                'featured_image' => 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?w=800&q=80',
                'image_alt' => 'Chef cocinando en sartén de hierro fundido',
                'content'  => '<h2>Por qué importan las técnicas</h2>
<p>Dos personas pueden tener exactamente los mismos ingredientes y obtener resultados completamente distintos si usan técnicas diferentes. Saber cuándo saltear, cuándo estofar y cuándo asar es la diferencia entre un cocinero intuitivo y un cocinero competente.</p>
<p>Las técnicas de cocción se dividen en dos grandes grupos: <strong>cocción por calor seco</strong> (sin agua) y <strong>cocción por calor húmedo</strong> (con agua o vapor).</p>

<h2>Calor seco: Saltear (Sauté)</h2>
<p>Saltear consiste en cocinar alimentos cortados pequeño en poca grasa a fuego alto, moviéndolos constantemente. Es la técnica más rápida de la cocina y la mejor para preservar el color, la textura crujiente y el valor nutricional de los vegetales.</p>
<p><strong>Temperatura:</strong> 200-220°C. La sartén debe estar muy caliente antes de añadir los alimentos.<br>
<strong>Ideal para:</strong> vegetales, mariscos, carnes en tiras, huevos revueltos.</p>

<h2>Calor seco: Asar a la plancha (Grilling/Searing)</h2>
<p>La plancha o la sartén de hierro fundido a temperatura muy alta crea la famosa reacción de Maillard en la superficie de la carne: una costra dorada y crujiente llena de sabor. Este proceso no "sella" el jugo (eso es un mito), pero sí crea cientos de compuestos aromáticos nuevos.</p>
<p><strong>Temperatura:</strong> 230-260°C.<br>
<strong>Regla de oro:</strong> Seca bien la proteína antes de asarla. La humedad en la superficie impide la reacción de Maillard y produce vapor en lugar de dorado.</p>

<h2>Calor húmedo: Hervir</h2>
<p>Hervir es cocinar en agua a 100°C. Es la técnica correcta para pastas, tubérculos (papa, yuca, plátano) y huevos. Importante: la pasta debe hervir en abundante agua salada (como agua de mar). El agua salada tiene mayor temperatura de ebullición y la sal se integra en la pasta durante la cocción.</p>

<h2>Calor húmedo: Estofar</h2>
<p>Estofar es cocinar a fuego lento en líquido cubriendo parcialmente el alimento. Es la técnica ideal para cortes de carne duros: el colágeno se disuelve lentamente en gelatina, produciendo una textura untuosa y jugosa que no se puede conseguir de otra forma.</p>
<p><strong>Temperatura:</strong> 80-90°C (por debajo del punto de ebullición).<br>
<strong>Tiempo:</strong> 1.5 a 4 horas dependiendo del corte.<br>
<strong>Ideal para:</strong> rabo de toro, falda de res, cerdo en trozos, cordero.</p>

<h2>Calor mixto: Braisear (Brasear)</h2>
<p>Brasear combina dorado inicial a fuego alto (para crear Maillard en la superficie) seguido de cocción lenta en poco líquido tapado. Es la técnica que produce los guisos y estofados más complejos en sabor, como el coq au vin francés, el seco de carne ecuatoriano o el birria mexicano.</p>

<h2>La temperatura interna es más importante que el tiempo</h2>
<p>El tiempo de cocción es siempre orientativo. Lo que realmente importa es la temperatura interna del alimento. Para ello, un termómetro de cocina es la herramienta más útil que puedes tener en tu cocina:</p>
<ul>
<li>Pollo: 74°C mínimo en la parte más gruesa</li>
<li>Res (término medio): 57-60°C</li>
<li>Res (bien cocido): 70°C+</li>
<li>Cerdo: 63°C mínimo</li>
<li>Pescado: 60-63°C</li>
</ul>',
                'seo_title'       => 'Técnicas de Cocción: Saltear, Estofar, Asar y Más — Guía Completa',
                'seo_description' => 'Domina las técnicas básicas de cocción: saltear, hervir, estofar, brasear y asar. Aprende cuándo y cómo aplicar cada técnica para cocinar mejor.',
                'published_at'    => Carbon::now()->subDays(18),
            ],

            /* ─── 7 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Cuchillos de Cocina: Cuál Usar para Cada Tarea',
                'slug'     => 'cuchillos-de-cocina-cual-usar-cada-tarea',
                'category' => 'Equipos de Cocina',
                'excerpt'  => 'Un buen cuchillo bien afilado hace el 80% del trabajo en la cocina. Conoce los tipos principales, cómo elegirlos, cómo cuidarlos y cuál es el correcto para cada preparación.',
                'featured_image' => 'https://images.unsplash.com/photo-1591189824344-9b53b30e2d70?w=800&q=80',
                'image_alt' => 'Set de cuchillos de cocina profesionales sobre tabla',
                'content'  => '<h2>El cuchillo más importante que puedes tener</h2>
<p>Si solo pudieras tener un cuchillo en tu cocina, debería ser un <strong>cuchillo de chef de 20-25 cm</strong>. Esta es la navaja suiza de la cocina: puede picar, cortar en juliana, deshuesar, filetear y triturar con el plano. Con un buen cuchillo de chef bien afilado, puedes hacer el 90% de las preparaciones de cocina.</p>

<h2>Los 5 cuchillos esenciales</h2>
<h3>1. Cuchillo de chef (Chef\'s knife)</h3>
<p>Hoja ancha y larga (18-25 cm), ligeramente curvada. Es el cuchillo de trabajo principal. La curva de la hoja permite balancear el cuchillo sobre la tabla al picar.</p>

<h3>2. Cuchillo puntilla (Paring knife)</h3>
<p>Pequeño (8-10 cm), hoja rígida y punta afilada. Perfecto para pelar, tornear verduras, desvenar camarones y trabajos de precisión donde el cuchillo de chef es demasiado grande.</p>

<h3>3. Cuchillo de pan (Bread knife)</h3>
<p>Hoja larga con sierra. No se usa solo para pan: es excelente para tomates, melones y cualquier alimento con exterior duro e interior blando. Nunca necesita afilado convencional.</p>

<h3>4. Cuchillo deshuesador (Boning knife)</h3>
<p>Hoja delgada y flexible (15-18 cm). Diseñado para separar carne del hueso y deshuesar aves. Su flexibilidad permite seguir los contornos del hueso con precisión.</p>

<h3>5. Mandolina (no es cuchillo, pero cuenta)</h3>
<p>Para cortes uniformes muy finos que ningún cuchillo puede igualar en velocidad. Indispensable para gratines, escabeches y ensaladas donde el grosor uniforme importa.</p>

<h2>Acero alemán vs. acero japonés</h2>
<p>Los cuchillos alemanes (Wüsthof, Henckels) tienen acero más blando (HRC 56-58), son más resistentes a impactos, más fáciles de afilar y mejor para uso intensivo diario. Los cuchillos japoneses (Global, Shun, MAC) tienen acero más duro (HRC 60-65+), mantienen el filo más tiempo pero son más frágiles y difíciles de afilar. Para un cocinero doméstico, un buen alemán es más práctico.</p>

<h2>El afilado: la habilidad olvidada</h2>
<p>Un cuchillo sin filo es más peligroso que uno bien afilado, porque requiere más fuerza para cortar y puede desviarse. El sistema de afilado más accesible para el hogar es la <strong>piedra de agua</strong> (whetstone) combinada con un eslabón de mantenimiento semanal.</p>
<p>Secuencia recomendada:</p>
<ol>
<li>Piedra grano 1000 para restaurar el filo perdido (cada 3-6 meses).</li>
<li>Piedra grano 3000-6000 para pulir y refinar el filo.</li>
<li>Eslabón de cerámica para mantenimiento semanal entre afilados completos.</li>
</ol>

<h2>Cuidado y almacenamiento</h2>
<p>Nunca metas un buen cuchillo en el lavavajillas: el detergente y los golpes contra otros utensilios deterioran el filo. Lávalo a mano y sécalo inmediatamente. Guárdalo en un bloque de madera, banda magnética o funda individual, nunca suelto en un cajón.</p>',
                'seo_title'       => 'Cuchillos de Cocina: Tipos, Usos y Cómo Elegir el Mejor',
                'seo_description' => 'Guía completa de cuchillos de cocina: cuál usar para cada tarea, diferencia entre acero alemán y japonés, cómo afilar y cómo cuidarlos correctamente.',
                'published_at'    => Carbon::now()->subDays(22),
            ],

            /* ─── 8 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Quinoa: Beneficios Nutricionales y Cómo Cocinarla Correctamente',
                'slug'     => 'quinoa-beneficios-nutricionales-como-cocinarla',
                'category' => 'Nutrición',
                'excerpt'  => 'La quinoa es uno de los pocos alimentos vegetales con proteína completa. Aprende por qué la NASA la llama el alimento del futuro, sus beneficios reales y la técnica para cocinarla perfecta.',
                'featured_image' => 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=800&q=80',
                'image_alt' => 'Quinoa cocida en bowl con vegetales frescos',
                'content'  => '<h2>Qué es la quinoa y de dónde viene</h2>
<p>La quinoa (<em>Chenopodium quinoa</em>) es una pseudocereal andina cultivada en los altiplanos de Bolivia, Perú y Ecuador desde hace más de 5.000 años. Para los incas era un alimento sagrado: la llamaban <em>chisiya mama</em> ("madre de todos los granos") y el Inca en persona plantaba el primer surco de la temporada con una herramienta de oro.</p>
<p>Técnicamente no es un cereal, sino el fruto de una planta del mismo género que la espinaca y la remolacha. Esto explica parte de su excepcional perfil nutricional.</p>

<h2>Por qué la NASA la considera un alimento del futuro</h2>
<p>En los años 90, la NASA estudió la quinoa como posible alimento para misiones de larga duración al espacio. La razón: la quinoa contiene los 9 aminoácidos esenciales en proporciones adecuadas para el ser humano, convirtiéndola en una proteína completa de origen vegetal. Esto es extraordinariamente raro en el reino vegetal.</p>

<h2>Perfil nutricional completo (por 100g cocida)</h2>
<ul>
<li><strong>Calorías:</strong> 120 kcal</li>
<li><strong>Proteínas:</strong> 4.4 g (proteína completa)</li>
<li><strong>Carbohidratos:</strong> 21.3 g (índice glucémico bajo: 53)</li>
<li><strong>Grasas:</strong> 1.9 g (principalmente omega-6 y omega-3)</li>
<li><strong>Fibra:</strong> 2.8 g</li>
<li><strong>Hierro:</strong> 1.5 mg (el doble que el trigo)</li>
<li><strong>Magnesio:</strong> 64 mg</li>
<li><strong>Zinc:</strong> 1.1 mg</li>
<li>Sin gluten (apta para celíacos)</li>
</ul>

<h2>La saponina: por qué hay que lavarla</h2>
<p>La quinoa contiene una sustancia llamada saponina en su capa exterior. La saponina es un compuesto amargo de sabor jabonoso que la planta produce como defensa contra insectos y pájaros. Si no se elimina, la quinoa cocida tendrá un sabor amargo y astringente.</p>
<p><strong>Cómo eliminarla:</strong> Pon la quinoa en un colador de malla fina bajo agua fría corriente y frota con las manos durante 1-2 minutos hasta que el agua salga clara y no forme espuma.</p>

<h2>La técnica perfecta para cocinarla</h2>
<p>La quinoa mal cocida queda pastosa o dura. La proporción y el método son fundamentales:</p>
<ol>
<li>Lava la quinoa como se indicó arriba.</li>
<li>Usa proporción 1:1.75 (1 taza de quinoa + 1¾ tazas de agua o caldo).</li>
<li>Lleva a ebullición con sal, tapa y reduce a fuego muy bajo.</li>
<li>Cocina 15 minutos exactos sin destapar.</li>
<li>Retira del fuego y deja reposar tapada 5 minutos.</li>
<li>Esponja con un tenedor.</li>
</ol>
<p>Sabrás que está lista cuando el germen (la espiral blanca) se haya separado del grano y las esferas sean completamente translúcidas.</p>

<h2>Usos en la cocina latinoamericana</h2>
<p>En Ecuador, Perú y Bolivia la quinoa se usa en sopas, tamales, atoles, pan y como sustituto del arroz. El locro de quinoa es uno de los platos más nutritivos de la cocina andina. La harina de quinoa enriquece panes y pasteles con proteínas extra.</p>',
                'seo_title'       => 'Quinoa: Beneficios, Proteína Completa y Cómo Cocinarla Perfecta',
                'seo_description' => 'Todo sobre la quinoa: por qué es proteína completa, sus beneficios reales, cómo eliminar la saponina y la técnica perfecta para cocinarla sin que quede pastosa.',
                'published_at'    => Carbon::now()->subDays(26),
            ],

            /* ─── 9 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Historia del Mole: 500 Años de Tradición Mexicana',
                'slug'     => 'historia-del-mole-500-anos-tradicion-mexicana',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'El mole es la salsa más compleja del mundo: puede llevar hasta 40 ingredientes y su preparación puede tomar tres días. Descubre la historia, los tipos y los secretos de este tesoro gastronómico mexicano.',
                'featured_image' => 'https://images.unsplash.com/photo-1613514785940-daed07799d9b?w=800&q=80',
                'image_alt' => 'Mole negro mexicano con ingredientes sobre mesa',
                'content'  => '<h2>El mole: la salsa más compleja del mundo</h2>
<p>Pocas salsas en el mundo tienen la complejidad histórica, cultural y gastronómica del mole mexicano. Puede contener entre 20 y 40 ingredientes distintos, su preparación puede tomar entre uno y tres días, y cada familia, cada pueblo y cada región tienen su versión propia, celosamente guardada.</p>
<p>La palabra mole proviene del náhuatl <em>molli</em>, que significa simplemente "salsa". Pero hay nada de simple en su elaboración.</p>

<h2>Los orígenes precolombinos</h2>
<p>Las salsas de chile existían en Mesoamérica mucho antes de la conquista española. Los aztecas preparaban <em>chilmolli</em>, una mezcla de chiles, tomates y especias molidas en metate. Estos preparados eran ofrendas rituales a los dioses y alimento ceremonial en banquetes reales.</p>
<p>La versión que conocemos hoy del mole, con cacao, especias europeas y decenas de ingredientes, nació del encuentro colonial entre la tradición indígena y los nuevos ingredientes que trajeron los españoles.</p>

<h2>La leyenda del convento de Puebla</h2>
<p>La tradición más citada atribuye la invención del mole poblano a las monjas del Convento de Santa Catalina de Siena en Puebla, en el siglo XVII. Según la leyenda, el arzobispo de México realizó una visita al convento, y la madre superiora, en apuros por no tener ingredientes suficientes, reunió todo lo que tenía: chiles secos, especias, chocolate, frutos secos, un pavo y distintas hierbas. El resultado sorprendió tanto al arzobispo que la salsa se convirtió en símbolo de Puebla.</p>
<p>La mayoría de historiadores cuestiona la leyenda, pero el mole poblano sí tiene en Puebla su expresión más elaborada y reconocida.</p>

<h2>Los siete moles de Oaxaca</h2>
<p>Oaxaca es la región con mayor diversidad de moles en México. Los "siete moles oaxaqueños" son:</p>
<ul>
<li><strong>Mole negro</strong>: el más complejo, con chilhuacle negro, chocolate y más de 30 ingredientes.</li>
<li><strong>Mole rojo</strong>: base de chilhuacle rojo y tomate.</li>
<li><strong>Coloradito</strong>: rojo profundo, más dulce y menos picante.</li>
<li><strong>Amarillo</strong>: con chile ancho y chilcostle, de sabor terroso.</li>
<li><strong>Verde</strong>: hecho con tomatillo, hierba santa y epazote.</li>
<li><strong>Chichilo</strong>: base de frijol negro y chile mulato.</li>
<li><strong>Manchamanteles</strong>: con frutas tropicales y chile ancho.</li>
</ul>

<h2>El cacao: el ingrediente que lo diferencia todo</h2>
<p>El cacao no siempre está presente en el mole, pero cuando lo está, transforma radicalmente el perfil de sabor. No lo hace dulce (como el chocolate procesado moderno), sino más profundo, con notas amargas que equilibran el picor del chile y la acidez del tomate. Los aztecas consideraban el cacao sagrado, y su presencia en el mole le da una dimensión ceremonial que persiste hasta hoy.</p>

<h2>Por qué el mole de tres días sabe mejor</h2>
<p>Los chiles secos se tuestan, se remojan y se muelen por separado. Los tomates se asan. Los frutos secos se fríen. Las especias se tuestan en comal seco. Cada paso desarrolla capas de sabor distintas. Cuando todo se une y se cocina lentamente con el caldo de pavo o pollo, los sabores se integran y se redondean durante horas. El reposo de un día para otro permite que las moléculas aromáticas se disuelvan completamente en la grasa, produciendo una profundidad imposible de lograr en una sesión rápida.</p>',
                'seo_title'       => 'Historia del Mole Mexicano: Orígenes, Tipos y el Secreto del Cacao',
                'seo_description' => 'La historia completa del mole: orígenes aztecas, la leyenda del convento de Puebla, los 7 moles oaxaqueños y por qué el mole de tres días es insuperable.',
                'published_at'    => Carbon::now()->subDays(30),
            ],

            /* ─── 10 ────────────────────────────────────────────────────── */
            [
                'title'    => 'Guía de Aceite de Oliva: Cómo Elegir el Mejor para Cocinar',
                'slug'     => 'guia-aceite-oliva-como-elegir-mejor-para-cocinar',
                'category' => 'Ingredientes',
                'excerpt'  => 'No todos los aceites de oliva son iguales. Aprende a leer las etiquetas, entender la acidez, la fecha de cosecha y el punto de humo para elegir el aceite correcto en cada uso.',
                'featured_image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&q=80',
                'image_alt' => 'Botella de aceite de oliva virgen extra con aceitunas',
                'content'  => '<h2>La jerarquía del aceite de oliva</h2>
<p>El aceite de oliva tiene varias categorías oficiales reguladas por el Consejo Oleícola Internacional. De mejor a peor:</p>
<ul>
<li><strong>Aceite de oliva virgen extra (AOVE)</strong>: extraído mecánicamente en frío, sin defectos sensoriales, acidez ≤0.8%. El mejor para consumo en crudo.</li>
<li><strong>Aceite de oliva virgen</strong>: similares características pero con ligeros defectos organolépticos, acidez ≤2%.</li>
<li><strong>Aceite de oliva</strong> (mezcla): refinado (deodorizado y decolorado) mezclado con virgen. Ha perdido la mayoría de sus polifenoles.</li>
<li><strong>Aceite de orujo de oliva</strong>: extraído con solventes del residuo sólido. El de menor calidad.</li>
</ul>

<h2>La acidez: qué significa realmente</h2>
<p>La acidez del aceite de oliva indica el porcentaje de ácidos grasos libres que contiene. Un aceite con acidez de 0.1% es de altísima calidad; con 0.8% es el máximo para llamarse virgen extra. Pero la acidez baja no garantiza por sí sola un aceite excelente: también importan el sabor, el aroma y el contenido en polifenoles.</p>
<p>La acidez alta no significa que el aceite sepa ácido. Es un indicador de degradación de la grasa, no de sabor.</p>

<h2>La fecha de cosecha vs. la fecha de caducidad</h2>
<p>El error más común al comprar aceite de oliva es fijarse solo en la fecha de caducidad. Lo que realmente importa es la <strong>fecha de cosecha</strong> (campaña). Un buen aceite tiene vida óptima de 18-24 meses desde la cosecha. Más allá, pierde antioxidantes aunque siga siendo "comestible".</p>
<p>Busca botellas que indiquen: "Cosecha 2023/24" o "Campaña 2024". Un aceite de cosecha reciente siempre supera a uno antiguo de precio más alto.</p>

<h2>El punto de humo: el gran malentendido</h2>
<p>Existe la creencia popular de que el aceite de oliva no se puede usar para cocinar a alta temperatura. Esta es una verdad a medias. El AOVE tiene un punto de humo de 190-210°C —más que suficiente para saltear y freír a temperaturas domésticas (170-180°C)—. Además, su alto contenido en antioxidantes lo hace más estable al calor que aceites vegetales refinados.</p>
<p>Lo que no debes hacer: reutilizar el aceite de oliva una y otra vez o calentarlo hasta humear constantemente.</p>

<h2>Cómo distinguir un buen aceite en el paladar</h2>
<p>Un AOVE de calidad presenta tres sensaciones características al probarlo solo:</p>
<ol>
<li><strong>Frutado</strong>: aroma a fruta fresca, puede ser herbáceo, almendrado, o con notas de tomate verde.</li>
<li><strong>Amargo</strong>: ligero amargor en el paladar que proviene de los polifenoles.</li>
<li><strong>Picante</strong>: ligero picor en la garganta, también signo de polifenoles activos.</li>
</ol>
<p>Si el aceite huele a rancio, a cartón o simplemente a nada, es un aceite degradado sin importar su precio.</p>

<h2>Los mejores usos según el tipo</h2>
<ul>
<li><strong>AOVE de cosecha temprana, muy frutado</strong>: en crudo sobre ensaladas, carpaccios, toastas con tomate.</li>
<li><strong>AOVE equilibrado</strong>: para sofritos, asados de verdura, marinadas.</li>
<li><strong>Aceite de oliva refinado o mezcla</strong>: para frituras profundas donde el sabor fuerte del AOVE no es deseable.</li>
</ul>',
                'seo_title'       => 'Guía Completa del Aceite de Oliva: Cómo Elegir, Conservar y Usar',
                'seo_description' => 'Aprende a elegir el mejor aceite de oliva: diferencias entre tipos, qué significa la acidez, la fecha de cosecha y cómo usarlo correctamente en la cocina.',
                'published_at'    => Carbon::now()->subDays(35),
            ],

            /* ─── 11 ────────────────────────────────────────────────────── */
            [
                'title'    => 'Cómo Marinar Carnes: Ciencia y Técnica para Resultados Perfectos',
                'slug'     => 'como-marinar-carnes-ciencia-tecnica-resultados-perfectos',
                'category' => 'Técnicas',
                'excerpt'  => 'Marinar correctamente una carne cambia completamente su sabor y textura. Pero no todos los adobos funcionan igual. Aprende la ciencia detrás del marinado y los errores más comunes.',
                'featured_image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
                'image_alt' => 'Carne marinada con especias y hierbas frescas',
                'content'  => '<h2>Qué hace un marinado: la ciencia</h2>
<p>Un marinado tiene tres funciones principales: <strong>aromatizar</strong>, <strong>ablandar</strong> y —en menor medida— <strong>conservar</strong> la carne. Cada función depende de componentes distintos del marinado:</p>
<ul>
<li><strong>Aromatizar</strong>: hierbas, especias, ajo, jengibre. Estos compuestos son liposolubles: se integran mejor en marinados con grasa (aceite).</li>
<li><strong>Ablandar</strong>: ácidos (limón, vinagre, yogur, vino) que desnaturalizan parcialmente las proteínas; o enzimas proteolíticas presentes en piña, papaya y kiwi que rompen las fibras musculares directamente.</li>
<li><strong>Conservar</strong>: la sal y el ácido inhiben el crecimiento bacteriano. El marinado prolonga la vida del producto 1-2 días adicionales en refrigeración.</li>
</ul>

<h2>El problema con el ácido en exceso</h2>
<p>Marinar carne en ácido durante demasiado tiempo no la ablanda: la "cocina" en frío, desnaturalizando las proteínas de la superficie y produciendo una textura seca y fibrosa. El ceviche funciona así intencionalmente, pero en una carne que luego vas a cocinar al fuego, el ácido en exceso es contraproducente.</p>
<p><strong>Regla:</strong> Para carnes que van al fuego, limita el ácido a no más del 30% del marinado. El tiempo máximo con mucho ácido: 2-4 horas para pescado; 6-12 horas para pollo; 12-24 horas para res y cerdo.</p>

<h2>Las enzimas: la solución para cortes duros</h2>
<p>Las enzimas proteolíticas de la piña fresca (bromelina), la papaya (papaína) y el kiwi (actinidina) son las mejores ablandantes naturales de carne. Pero son tan potentes que hay que usarlas con cuidado: más de 30-60 minutos y la carne tendrá una textura esponjosa y desagradable.</p>
<p>El <strong>yogur</strong> es la excepción: su acidez láctica suave y sus enzimas ablanadan la carne de forma gradual y equilibrada. Es por eso que el pollo tikka masala se marina en yogur 24-48 horas sin deteriorar la textura.</p>

<h2>La sal en el marinado: en qué momento añadirla</h2>
<p>Existe un debate eterno: ¿sal antes o después? La respuesta es más sutil. La sal en el marinado tiene efecto osmótico inicial: extrae la humedad de la carne. Pero si se deja tiempo suficiente (mínimo 2 horas, idealmente toda la noche), esa misma humedad —ahora con sal y aromas disueltos— es reabsorbida por la carne en un proceso llamado <em>osmosis reversa</em>. Esto produce la carne más jugosa y sabrosa.</p>
<p>Marinado corto (menos de 2 horas): sala la carne justo antes de cocinar. Marinado largo: sala desde el principio.</p>

<h2>Marinados base de la cocina latinoamericana</h2>
<h3>Adobo ecuatoriano</h3>
<p>Comino, achiote, ajo molido, cebolla, sal, naranja agria o limón. Perfecto para cerdo, pollo y cuy.</p>
<h3>Sazón puertorriqueño/dominicano</h3>
<p>Ajo, orégano, pimiento morrón, cilantro, comino, vinagre blanco y aceite. Base de toda la cocina caribeña.</p>
<h3>Chimichurri argentino</h3>
<p>Perejil, orégano, ajo, vinagre, aceite, ají molido. Se usa tanto para marinar como para acompañar carnes a la brasa.</p>',
                'seo_title'       => 'Cómo Marinar Carnes Correctamente: Ciencia, Técnica y Tiempos',
                'seo_description' => 'Aprende la ciencia del marinado: qué hace el ácido, las enzimas y la sal en la carne, cuánto tiempo marinar y los mejores adobos latinoamericanos.',
                'published_at'    => Carbon::now()->subDays(40),
            ],

            /* ─── 12 ────────────────────────────────────────────────────── */
            [
                'title'    => 'Fermentación en Casa: Cómo Preparar Chicha, Kombucha y Kimchi',
                'slug'     => 'fermentacion-en-casa-chicha-kombucha-kimchi',
                'category' => 'Técnicas',
                'excerpt'  => 'La fermentación es una de las técnicas más antiguas de la humanidad y está viviendo un renacimiento. Aprende a fermentar en casa sin equipos especiales y con resultados deliciosos.',
                'featured_image' => 'https://images.unsplash.com/photo-1626200838021-a93e2f36ab57?w=800&q=80',
                'image_alt' => 'Frascos de fermentación con vegetales y líquidos',
                'content'  => '<h2>Por qué fermentar en casa</h2>
<p>La fermentación es el proceso por el cual microorganismos (bacterias, levaduras) transforman los azúcares de los alimentos en ácidos, gases o alcohol. Es una de las técnicas culinarias más antiguas —anterior al fuego, según algunos antropólogos— y hoy sabemos que los alimentos fermentados tienen beneficios extraordinarios para la microbiota intestinal.</p>
<p>Fermentar en casa es más fácil de lo que parece. No necesitas equipos especiales: solo ingredientes frescos, sal, agua y paciencia.</p>

<h2>La ciencia de la fermentación láctica</h2>
<p>La fermentación láctica —la más usada en vegetales— es protagonizada por bacterias del género <em>Lactobacillus</em> que convierten los azúcares en ácido láctico. Este ácido baja el pH del medio, creando un ambiente hostil para bacterias dañinas y conservando el alimento de forma natural.</p>
<p>El ácido láctico es también el responsable del sabor ácido característico del chucrut, el kimchi, el yogur y muchos quesos.</p>

<h2>Chicha de jora: la bebida sagrada andina</h2>
<p>La chicha es la bebida fermentada más importante de los Andes. La chicha de jora se elabora con maíz germinado (jora) que contiene enzimas que convierten el almidón en azúcares fermentables.</p>
<p><strong>Proceso básico:</strong></p>
<ol>
<li>Germina maíz seco cubriéndolo con agua durante 3 días hasta que aparezcan raíces de 1 cm.</li>
<li>Seca el maíz germinado (jora) al sol 2 días.</li>
<li>Hierve la jora en 5 partes de agua durante 3 horas hasta que reduzca.</li>
<li>Cuela, enfría a temperatura ambiente y añade un poco de chicha anterior o levadura seca.</li>
<li>Fermenta en recipiente de barro o vidrio cubierto con tela 3-5 días.</li>
</ol>

<h2>Kombucha: el té fermentado</h2>
<p>La kombucha es té negro o verde fermentado por un SCOBY (Symbiotic Culture Of Bacteria and Yeast). El resultado es una bebida ligeramente efervescente, ácida y con notas de vinagre que hoy se vende a precios premium en todo el mundo.</p>
<p><strong>Para hacer 1 litro de kombucha:</strong></p>
<ol>
<li>Prepara 1 litro de té negro fuerte con 80g de azúcar. Enfría completamente.</li>
<li>Añade el SCOBY y 100ml de kombucha ya hecha (starter).</li>
<li>Cubre con tela y deja fermentar a temperatura ambiente 7-14 días.</li>
<li>Prueba diariamente: cuando el sabor ácido-dulce te guste, está lista.</li>
</ol>

<h2>Kimchi: el superalimento coreano</h2>
<p>El kimchi es col fermentada con ají, ajo, jengibre y mariscos secos. Está clasificado entre los alimentos más saludables del mundo por su densidad de probióticos y antioxidantes.</p>
<p><strong>Kimchi básico:</strong> Mezcla col china troceada con 2% de su peso en sal. Deja reposar 2 horas hasta que suelte agua. Escurre y mezcla con pasta de chile coreano (gochujang), ajo rallado, jengibre y cebolleta. Empuja en frasco de vidrio eliminando burbujas de aire. Fermenta a temperatura ambiente 1-3 días, luego refrigera.</p>

<h2>Seguridad en la fermentación casera</h2>
<p>La fermentación láctica es muy segura porque el ácido producido inhibe patógenos. Para mayor seguridad: usa utensilios limpios (no es necesario esterilizar), mantén los vegetales siempre sumergidos en el líquido (el oxígeno favorece hongos no deseados), y confía en tus sentidos: la fermentación bien hecha huele ácida y agradable, nunca a podredumbre.</p>',
                'seo_title'       => 'Fermentación en Casa: Chicha, Kombucha y Kimchi Paso a Paso',
                'seo_description' => 'Aprende a fermentar en casa: chicha de jora andina, kombucha de té y kimchi coreano. Técnicas seguras, fáciles y los beneficios probióticos de cada fermentado.',
                'published_at'    => Carbon::now()->subDays(45),
            ],

            /* ─── 13 ────────────────────────────────────────────────────── */
            [
                'title'    => 'La Pasta Perfecta: Errores Comunes y Cómo Evitarlos',
                'slug'     => 'pasta-perfecta-errores-comunes-como-evitarlos',
                'category' => 'Técnicas',
                'excerpt'  => 'La pasta parece simple pero tiene más secretos de los que imaginas. Descubre por qué el agua de pasta es oro líquido, por qué no debes añadir aceite al agua y cómo lograr el al dente perfecto.',
                'featured_image' => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=800&q=80',
                'image_alt' => 'Pasta fresca italiana sobre harina',
                'content'  => '<h2>Los errores universales que arruinan la pasta</h2>
<p>La pasta es uno de los alimentos más consumidos del mundo, y uno de los más mal preparados fuera de Italia. Los errores se repiten en millones de cocinas: agua insuficiente, sin sal, aceite en el agua, tirar la pasta a la pared para ver si está lista. Empecemos por desmentir los mitos.</p>

<h2>Mito 1: "Hay que añadir aceite al agua para que la pasta no se pegue"</h2>
<p>Este es el error más extendido. El aceite flota sobre el agua: no llega a la pasta mientras hierve. Lo que sí hace es recubrir la pasta después de escurrirla con una capa grasa que impide que la salsa se adhiera. Resultado: pasta grasosa con salsa encima, no integrada.</p>
<p><strong>La solución real:</strong> Suficiente agua, sal abundante, buena salsa y mezclar inmediatamente pasta con salsa fuera del fuego.</p>

<h2>Mito 2: "Tirar la pasta a la pared para saber si está lista"</h2>
<p>Si la pasta se pega a la pared es porque está sobrecocida y pegajosa, no en su punto. La única forma de saber si está al dente es probarla. Punto.</p>

<h2>La proporción correcta de agua y sal</h2>
<p>La pasta necesita abundante agua para cocinarse uniformemente: mínimo 1 litro por cada 100g de pasta. El agua debe estar "tan salada como el mar": aproximadamente 10g de sal por litro. Esto parece mucho, pero la pasta solo absorbe una fracción de esa sal durante la cocción.</p>
<p>La sal no solo condimenta: también eleva ligeramente el punto de ebullición y modifica la textura superficial de la pasta.</p>

<h2>El agua de pasta: el ingrediente secreto</h2>
<p>Cuando la pasta hierve, libera almidón en el agua. Este agua almidonada es extremadamente valiosa: actúa como emulsionante natural, ligando la salsa con la pasta y creando una textura untuosa sin necesidad de crema.</p>
<p>Antes de escurrir la pasta, guarda siempre al menos una taza de agua de cocción. Añádela cuchara a cuchara mientras integras la pasta con la salsa a fuego medio. La diferencia es enorme.</p>

<h2>El al dente: qué significa realmente</h2>
<p>Al dente literalmente significa "al diente" en italiano. Una pasta perfectamente al dente tiene una ligera resistencia en el centro cuando la muerdes: no está dura ni harinosa, pero tampoco blanda. La pasta continúa cocinándose con el calor residual después de escurrirla, así que retírala del agua 1-2 minutos antes de que el paquete indique.</p>

<h2>La técnica italiana de la mantecatura</h2>
<p>Los italianos terminan siempre la pasta en la sartén con la salsa: añaden la pasta escurrida (con agua de cocción reservada) directamente al sartén con la salsa caliente, y mezclan a fuego medio durante 1-2 minutos hasta que la salsa se integra completamente. Esto se llama <em>mantecatura</em>.</p>
<p>Para salsas con queso (cacio e pepe, carbonara): apaga el fuego o aleja del calor antes de añadir el queso o el huevo. El calor excesivo los cuaja y forma grumos en lugar de una crema lisa.</p>

<h2>Pasta fresca vs. pasta seca</h2>
<p>No existe "mejor" o "peor": son distintas. La pasta seca de sémola de trigo duro (Barilla, De Cecco) es ideal para salsas contundentes con tomate, guisos y ragú. La pasta fresca (hecha con harina 00 y yemas) es más delicada y se combina mejor con salsas de mantequilla, trufas, ricota o cremas ligeras.</p>',
                'seo_title'       => 'La Pasta Perfecta: Errores y Técnicas — Guía Definitiva',
                'seo_description' => 'Por qué no debes añadir aceite al agua, cómo usar el agua de pasta como salsa y la técnica italiana de la mantecatura para pasta perfecta siempre.',
                'published_at'    => Carbon::now()->subDays(50),
            ],

            /* ─── 14 ────────────────────────────────────────────────────── */
            [
                'title'    => '¿Qué es el Umami? El Quinto Sabor que Explica Por Qué Algunas Comidas Son Irresistibles',
                'slug'     => 'que-es-el-umami-quinto-sabor-comidas-irresistibles',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'El umami fue descubierto en 1908 por un científico japonés. Hoy sabemos que es el sabor que hace adictivos al parmesano, el tomate maduro, la salsa de soya y el caldo de huesos.',
                'featured_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&q=80',
                'image_alt' => 'Ingredientes ricos en umami: parmesano, tomate, soya, hongos',
                'content'  => '<h2>Los cinco sabores básicos</h2>
<p>Durante siglos, la ciencia occidental reconoció solo cuatro sabores básicos: dulce, salado, ácido y amargo. En 1908, el químico japonés Kikunae Ikeda cambió todo. Mientras estudiaba el dashi —el caldo base de la cocina japonesa hecho con alga kombu— notó que tenía un sabor particular que no encajaba en ninguna de las cuatro categorías existentes. Lo llamó <em>umami</em> (うまみ), que en japonés significa "sabor sabroso" o "sabor delicioso".</p>

<h2>Qué es el umami a nivel molecular</h2>
<p>Ikeda identificó que el umami provenía del ácido glutámico (glutamato) presente en el alga kombu. Décadas después, los científicos descubrieron que otros compuestos también activan el mismo receptor gustativo: los nucleótidos inosinato (presente en carnes) y guanilato (en hongos secos).</p>
<p>Lo extraordinario del umami es el <strong>efecto de sinergia</strong>: cuando el glutamato y el inosinato se combinan, el sabor umami se multiplica hasta 8 veces. Esto explica por qué el dashi (alga + bonito) o un ragú de carne con tomate (nucleótidos + glutamato) saben tan bien.</p>

<h2>Alimentos ricos en umami</h2>
<p>Algunos de los alimentos con mayor concentración de glutamato:</p>
<ul>
<li><strong>Parmesano Reggiano</strong>: 1.200 mg de glutamato por 100g — el más alto de todos los alimentos naturales.</li>
<li><strong>Anchoas en sal</strong>: 630 mg/100g — explica por qué añadir 2 anchoas a un ragú lo hace incomparablemente más profundo.</li>
<li><strong>Alga kombu seca</strong>: 1.608 mg/100g — la fuente original de umami.</li>
<li><strong>Tomate maduro</strong>: 246 mg/100g (concentrado de tomate: 1.140 mg/100g).</li>
<li><strong>Setas shiitake secas</strong>: 1.060 mg/100g.</li>
<li><strong>Salsa de soya</strong>: 780 mg/100g.</li>
</ul>

<h2>El umami en la cocina latinoamericana</h2>
<p>Aunque el término es japonés, el umami está presente en la cocina latinoamericana desde siempre. El hogao colombiano, el refrito ecuatoriano y el sofrito puertorriqueño —con tomate maduro cocinado lentamente— son bombas de umami. El caldo de huesos, que reduce durante horas concentrando colágeno y gelatina, es puro umami animal.</p>
<p>El camarón seco usado en el Ecuador y Brasil, las anchoas del Perú y los quesos curados de todo el continente son otras fuentes tradicionales de este quinto sabor.</p>

<h2>MSG: el umami artificial y los mitos</h2>
<p>El glutamato monosódico (MSG, o "ajinomoto") es la versión sintetizada del glutamato. Durante décadas fue demonizado por el llamado "síndrome del restaurante chino". Estudios doble ciego han demostrado que este síndrome no existe como entidad médica independiente: el MSG en dosis normales es completamente seguro para la gran mayoría de personas. La FDA lo clasifica como GRAS (Generally Recognized As Safe).</p>
<p>Los chefs modernos lo usan como herramienta de sabor con total normalidad, al igual que la sal.</p>',
                'seo_title'       => 'Qué es el Umami: El Quinto Sabor y los Alimentos Más Ricos en Él',
                'seo_description' => 'Descubre el umami: el quinto sabor, qué alimentos lo contienen (parmesano, tomate, anchoas), la sinergia de sabores y la verdad sobre el MSG.',
                'published_at'    => Carbon::now()->subDays(55),
            ],

            /* ─── 15 ────────────────────────────────────────────────────── */
            [
                'title'    => 'Cocinar con Fuego: Historia y Técnicas del Asado Latinoamericano',
                'slug'     => 'cocinar-con-fuego-historia-tecnicas-asado-latinoamericano',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'El asado es mucho más que una técnica: es un ritual social, una identidad cultural y una forma de entender la generosidad. De Argentina a Ecuador, el fuego une las mesas de América.',
                'featured_image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80',
                'image_alt' => 'Asado de carnes sobre parrilla con fuego y brasas',
                'content'  => '<h2>El fuego: la primera técnica culinaria</h2>
<p>Cocinar con fuego es la habilidad más antigua de la especie humana. Hace aproximadamente 1 millón de años, los homininos descubrieron que el calor transformaba los alimentos de maneras que el crudo no podía: los hacía más digeribles, más sabrosos y más seguros. La cocción con fuego es, literalmente, lo que nos hizo humanos.</p>
<p>En América Latina, el fuego directo —la brasa, el carbón, la leña— no es solo una técnica: es un lenguaje cultural. El asado argentino, el cuy asado ecuatoriano, el hornado, la barbacoa mexicana y la parrillada boliviana son rituales que trascienden la comida y celebran la comunidad.</p>

<h2>El asado argentino: el ritual más estudiado</h2>
<p>El asado argentino es una ceremonia que tiene roles claramente definidos: el <em>asador</em> es el sacerdote del fuego. Llegar al asado sin invitación es una falta de respeto. Apurar al asador es una ofensa. El asado empieza cuando el asador decide que está listo, nunca antes.</p>
<p>La técnica argentina es de paciencia radical: carbón o leña de quebracho, brasas —nunca llamas— y tiempo. Los cortes se cocinan lentamente a distancia del calor, absorbiendo el humo de la madera y caramelizando sin quemarse. El chorizo y la morcilla son los aperitivos que se comen mientras la carne principal termina.</p>

<h2>El cuy asado ecuatoriano: el plato de fiesta andina</h2>
<p>El cuy (cobaya) fue domesticado en los Andes hace más de 5.000 años como fuente principal de proteína animal. Hoy sigue siendo el plato de celebración por excelencia en las sierras de Ecuador, Perú y Bolivia.</p>
<p>El cuy se marina en achiote, ajo y especias andinas, y se cocina entero en palo giratorio sobre brasas de leña, o abierto sobre una parrilla. La piel queda crujiente como chicharrón; la carne, oscura y jugosa. En muchos pueblos de la sierra ecuatoriana, servir cuy asado es el mayor honor que una familia puede ofrecer a un invitado.</p>

<h2>La barbacoa mexicana: cocina subterránea</h2>
<p>La barbacoa original mexicana no tiene nada que ver con la parrilla al aire libre estadounidense. La barbacoa tradicional se cocina bajo tierra: se cava un hoyo, se calientan piedras con fuego durante horas, se coloca la carne (generalmente cabeza de res o borrego) envuelta en pencas de maguey, se tapa con tierra y se deja cocinar con el vapor y el calor radial durante 8-12 horas.</p>
<p>El resultado es una carne desmenuzada, gelatinosa y de sabor profundo imposible de reproducir con ninguna otra técnica. La grasa y el colágeno se convierten en consomé natural que se sirve como caldo antes de la carne.</p>

<h2>Brasas vs. llamas: la diferencia técnica</h2>
<p>El error más común al asar a la parrilla es cocinar sobre llamas activas. Las llamas producen calor directo irregular que quema el exterior antes de cocer el interior, además de depositar compuestos indeseados de la combustión incompleta sobre la carne.</p>
<p>Las brasas —carbón o leña completamente quemados, sin llama visible— producen calor radiante uniforme, controlable y limpio. La temperatura se regula alejando o acercando la parrilla, no soplando las brasas.</p>
<p><strong>Cómo saber si las brasas están listas:</strong> Mantén la palma de la mano a 10 cm de las brasas. Si puedes aguantar 3-4 segundos: temperatura media (ideal para pollo y cerdo). Si solo puedes 1-2 segundos: temperatura alta (ideal para steak).</p>',
                'seo_title'       => 'Asado Latinoamericano: Historia, Técnicas del Fuego y Rituales',
                'seo_description' => 'Del asado argentino al cuy ecuatoriano y la barbacoa mexicana: historia del fuego en la cocina latinoamericana, técnicas de brasas y el ritual social del asado.',
                'published_at'    => Carbon::now()->subDays(60),
            ],

        ];

        foreach ($posts as $data) {
            Post::create(array_merge($data, ['is_published' => true]));
        }
    }
}
