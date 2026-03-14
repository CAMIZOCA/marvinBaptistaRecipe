<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BlogArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [

            /* ─── 1 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Cómo Hacer el Sofrito Perfecto: La Base de Toda la Cocina Latinoamericana',
                'slug'     => 'como-hacer-sofrito-perfecto',
                'category' => 'Técnicas',
                'excerpt'  => 'El sofrito es el alma de la cocina latinoamericana. Aprende la técnica exacta para crear esta base aromática que transforma cualquier guiso, arroz o estofado en un plato lleno de sabor y profundidad.',
                'featured_image' => 'https://images.unsplash.com/photo-1606923829579-0cb981a83e2e?w=800&q=80',
                'image_alt' => 'Sofrito de cebolla, ajo y tomate en sartén',
                'seo_title' => 'Cómo Hacer el Sofrito Perfecto Paso a Paso',
                'seo_description' => 'Aprende la técnica del sofrito latinoamericano: proporciones exactas, tiempos de cocción, errores comunes y variantes regionales. La base que todo chef conoce.',
                'content'  => '<h2>¿Qué es el sofrito y por qué importa tanto?</h2>
<p>Si hay un concepto que unifica las cocinas de Ecuador, Colombia, Cuba, Puerto Rico y Venezuela, es el sofrito. Este refrito de verduras aromáticas, cocinado lentamente en aceite o manteca, es la primera capa de sabor sobre la que se construye prácticamente cualquier plato de la gastronomía latinoamericana.</p>
<p>No es exageración decir que un sofrito bien hecho puede elevar un plato mediocre al nivel de restaurante, mientras que uno mal ejecutado —quemado, apresurado o con ingredientes de mala calidad— arruinará incluso los mejores ingredientes que pongas después.</p>
<p>En este artículo desglosamos la técnica completa, los ingredientes esenciales según cada región, los errores más comunes y algunos secretos de chef que marcan la diferencia.</p>

<h2>Los ingredientes base: la trinidad aromática</h2>
<p>Aunque el sofrito varía por región, existe una trinidad de ingredientes que aparece en casi todas sus versiones:</p>
<ul>
<li><strong>Cebolla blanca o paiteña:</strong> la base dulce y sulfurosa que carameliza con el calor. Nunca la reemplaces con cebolla morada en un sofrito: su dulzor distinto cambia completamente el perfil de sabor.</li>
<li><strong>Ajo:</strong> dos o tres dientes por cada cebolla mediana. El ajo debe agregarse después de la cebolla, no al mismo tiempo, ya que se quema más rápido y amargaría el sofrito.</li>
<li><strong>Tomate riñón:</strong> rallado directamente en la sartén, no picado. Rallarlo libera el jugo y la pulpa de manera uniforme, integrándose perfectamente con la cebolla sin dejar trozos duros.</li>
</ul>

<h2>La técnica paso a paso</h2>
<p>Un buen sofrito no se hace con prisa. El proceso completo lleva entre 15 y 20 minutos a fuego medio-bajo, y cada etapa tiene un propósito concreto.</p>

<h3>Paso 1: El aceite y la temperatura</h3>
<p>Usa aceite de girasol o aceite vegetal neutro para la mayoría de sofritos latinoamericanos. El aceite de oliva extra virgen tiene un punto de humo más bajo y puede aportar un sabor que no siempre encaja. Calienta el aceite a fuego medio hasta que esté brillante pero sin humear: unos 2-3 minutos.</p>
<p>Si quieres un sabor más profundo, puedes empezar con un par de semillas de achiote en el aceite frío, calentarlas hasta que el aceite tome color naranja y luego retirarlas. Ese aceite rojo aromatizado es la base del sofrito ecuatoriano y venezolano.</p>

<h3>Paso 2: La cebolla primero</h3>
<p>Añade la cebolla finamente picada y una pizca de sal. La sal ayuda a que la cebolla libere su agua más rápido, acelerando el proceso de ablandamiento. Cocina a fuego medio-bajo, revolviendo cada dos minutos, durante unos 8-10 minutos. La cebolla debe quedar traslúcida y empezar a dorarse ligeramente en los bordes. Si se dora demasiado rápido, baja el fuego y agrega una cucharada de agua para desglasear el fondo.</p>

<h3>Paso 3: El ajo</h3>
<p>Agrega el ajo machacado o finamente picado y cocina por 1-2 minutos más, revolviendo constantemente. El ajo pasa de crudo a quemado en cuestión de segundos si el fuego es muy alto. Busca que tome un color dorado pálido y que el aroma sea suave y fragante, no punzante.</p>

<h3>Paso 4: Los pimientos (opcional pero recomendado)</h3>
<p>En la cocina ecuatoriana y caribeña, el ají pimiento rojo o verde se agrega junto con la cebolla. En Cuba y Puerto Rico se usa el ají cachucha o cubanelle, que aporta dulzor sin picante. Pícalo finamente y agrégalo al mismo tiempo que la cebolla.</p>

<h3>Paso 5: El tomate</h3>
<p>Ralla dos tomates medianos directamente sobre la sartén. El jugo ayudará a levantar cualquier punto dorado del fondo (ese fondo tostado es puro sabor). Sube ligeramente el fuego a medio y cocina revolviendo durante 5-7 minutos más, hasta que el tomate pierda su acidez cruda y el sofrito tome una consistencia de pasta espesa y brillante.</p>

<h2>Las variantes regionales que debes conocer</h2>
<p><strong>Ecuador — Refrito con achiote:</strong> Cebolla blanca, ajo, tomate riñón, culantro de pozo (cilantro de hoja larga), achiote en aceite. Es la base del seco de pollo, el encebollado y casi toda la cocina de costa.</p>
<p><strong>Cuba y Puerto Rico — Sofrito caribeño:</strong> Cebolla, ajo, ají cubanelle, ají dulce, tomate, culantro (recao), orégano. A veces incluye pimiento morrón asado. Sirve para arroz con pollo, frijoles negros y ropa vieja.</p>
<p><strong>Colombia — Hogao:</strong> Tomate chonto y cebolla larga (cebolla junca) en proporciones iguales, cocinados hasta formar una pasta densa. Sin ajo ni pimiento. Es la base del bandeja paisa y los frijoles antioqueños.</p>
<p><strong>México — Recado:</strong> Tomate, cebolla, chile, ajo tatemados directamente en el comal hasta que tengan manchas negras, luego molidos en mortero. El tostado aporta una profundidad ahumada única.</p>

<h2>Los errores más comunes (y cómo evitarlos)</h2>
<p><strong>Error 1: Fuego muy alto.</strong> El sofrito necesita tiempo. El fuego alto quema el ajo y la cebolla antes de que se cocinen correctamente, dejando un sabor amargo. Paciencia.</p>
<p><strong>Error 2: Agregar el ajo con la cebolla cruda.</strong> El ajo necesita mucho menos tiempo. Si lo agregas al inicio, estará quemado cuando la cebolla apenas esté empezando a ablandarse.</p>
<p><strong>Error 3: No salar desde el principio.</strong> La sal ayuda a que la cebolla suelte su agua y se cocine de manera uniforme. Sin sal, la cebolla tarda mucho más y puede quedar con zonas crudas.</p>
<p><strong>Error 4: Usar tomates sin madurar.</strong> Un tomate verde o de temporada baja tiene más acidez y menos azúcares. Asegúrate de usar tomates bien maduros, aunque sean del supermercado.</p>

<h2>Cómo conservar el sofrito</h2>
<p>El sofrito hecho en grandes cantidades se congela perfectamente. Prepara el doble o el triple de la receta y guárdalo en cubetas de hielo. Una vez congelados, guarda los cubos en una bolsa zip y úsalos directamente desde el congelador: se deshielan en cuestión de minutos al contacto con la sartén caliente.</p>
<p>En refrigeración, el sofrito dura hasta 5 días en un recipiente hermético con una fina capa de aceite encima para evitar la oxidación.</p>',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],

            /* ─── 2 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Guía Completa de Especias Mediterráneas: Historia, Usos y Cómo Combinarlas',
                'slug'     => 'guia-especias-mediterraneas',
                'category' => 'Ingredientes',
                'excerpt'  => 'El tomillo, el romero, la canela, el azafrán y el za\'atar son las especias que definen la cocina mediterránea. Conoce su historia, sus mejores combinaciones y los errores que cometen incluso los cocineros con experiencia.',
                'featured_image' => 'https://images.unsplash.com/photo-1532336414038-cf19250c5757?w=800&q=80',
                'image_alt' => 'Especias mediterráneas: tomillo, romero, canela y azafrán',
                'seo_title' => 'Guía de Especias Mediterráneas: Usos y Combinaciones',
                'seo_description' => 'Descubre las especias esenciales de la cocina mediterránea: tomillo, romero, azafrán, za\'atar y más. Historia, usos culinarios y combinaciones perfectas.',
                'content'  => '<h2>La tradición de las especias en el Mediterráneo</h2>
<p>Durante siglos, las rutas de las especias conectaron el Mediterráneo con Oriente. Fenicio, griegos, romanos y árabes construyeron imperios sobre el comercio de la canela, el azafrán y la pimienta. Hoy, esas mismas especias siguen siendo el corazón de las cocinas de España, Italia, Grecia, Marruecos y el Líbano.</p>
<p>Entender cómo usar correctamente cada especia mediterránea es la diferencia entre un plato plano y uno que transporta al comensal a una terraza frente al mar.</p>

<h2>Las diez especias mediterráneas fundamentales</h2>

<h3>1. Tomillo (Thymus vulgaris)</h3>
<p>El tomillo es quizás la hierba más versátil de la cocina mediterránea. Con un sabor cálido, ligeramente floral y con notas de menta, funciona tanto fresco como seco. Es fundamental en el <em>bouquet garni</em> francés, en los guisos de cordero griego, en el sofrito español y en las salsas italianas de tomate.</p>
<p><strong>Combina perfectamente con:</strong> ajo, laurel, romero, limón, tomato, cordero y pollo.</p>
<p><strong>Cuándo agregarlo:</strong> Al principio de la cocción para sabores profundos, o al final para un aroma más fresco.</p>

<h3>2. Romero (Salvia rosmarinus)</h3>
<p>Con su aroma resinoso e intenso, el romero es la especia del Mediterráneo occidental. Se usa en carnes asadas —especialmente cordero, cerdo y pollo—, en focaccia, en aceites infusionados y en papas al horno. Tiene un sabor muy potente: una ramita es suficiente para aromatizar un guiso entero.</p>
<p><strong>Error común:</strong> Usar demasiado romero seco, que puede dominar cualquier plato. Si usas romero seco, reduce la cantidad a la mitad respecto al fresco.</p>

<h3>3. Orégano mediterráneo</h3>
<p>El orégano mediterráneo (especialmente el griego) es completamente diferente al orégano mexicano. Más suave, más floral, con notas de miel y cítrico. Es esencial en la pizza italiana, en la ensalada griega, en el souvlaki y en el aderezo de shawarma.</p>
<p>Se usa casi siempre seco: el secado concentra sus aceites esenciales y potencia el sabor.</p>

<h3>4. Azafrán (Crocus sativus)</h3>
<p>El azafrán es la especia más cara del mundo por peso, pero se usa en cantidades microscópicas. Unos pocos hilos son suficientes para colorear y aromatizar una paella para seis personas. Su sabor es floral, ligeramente metálico y con notas de miel.</p>
<p><strong>Cómo usarlo correctamente:</strong> Nunca lo eches directamente al guiso. Infusiona los hilos en 3-4 cucharadas de agua caliente o caldo durante al menos 10 minutos, y agrega ese líquido al plato. Así extraes el máximo color y sabor.</p>

<h3>5. Canela de Ceilán</h3>
<p>En el Mediterráneo, especialmente en la cocina árabe, marroquí y griega, la canela no es solo para postres. Se usa en guisos de carne con frutos secos (el <em>tagine</em> marroquí), en el <em>pastitsio</em> griego y en el arroz con especias del Líbano. La canela de Ceilán (la "verdadera") es más dulce y delicada que la casia china que se vende comúnmente.</p>

<h3>6. Comino del Mediterráneo</h3>
<p>Presente desde Marruecos hasta Turquía, el comino mediterráneo tiene un perfil más suave y tostado que el latinoamericano. Se usa en el hummus libanés, en el falafel, en el ras el hanout marroquí y en las salchichas griegas.</p>

<h3>7. Za\'atar</h3>
<p>El za\'atar es una mezcla de especias del Levante (Líbano, Siria, Palestina, Israel) que combina tomillo seco, sésamo tostado, zumaque (sumac) y sal. Se mezcla con aceite de oliva y se unta en pan plano, o se usa como marinada para pollo. El zumaque le da una acidez cítrica única.</p>

<h3>8. Pimentón ahumado (Paprika española)</h3>
<p>El pimentón de La Vera, ahumado sobre leña de roble, es el alma de la chorizo español, el pulpo a la gallega y el sofrito andaluz. Su sabor dulce y ahumado a la vez no tiene sustituto real. Existe en tres versiones: dulce, agridulce y picante.</p>

<h3>9. Hinojo (semillas)</h3>
<p>Las semillas de hinojo tienen un sabor anisado suave que aparece en la salchicha italiana <em>finocchiona</em>, en el pan provenzal, en el bouillabaisse francés y en los mariscos del sur de Italia. Combina especialmente bien con cerdo y pescados blancos.</p>

<h3>10. Laurel</h3>
<p>Discreto pero indispensable. Ningún caldo, guiso de legumbres o estofado mediterráneo está completo sin una o dos hojas de laurel. Hay que recordar retirarlas antes de servir: comerlas no es agradable. El laurel fresco tiene un sabor más intenso que el seco.</p>

<h2>Cómo crear tus propias mezclas mediterráneas</h2>
<p><strong>Herbes de Provence (Provenza, Francia):</strong> Tomillo + romero + orégano + lavanda + mejorana. Para pollo asado, quiches y verduras al horno.</p>
<p><strong>Ras el Hanout (Marruecos):</strong> Canela + comino + cilantro + jengibre + pimienta negra + cúrcuma + clavo. Cada cocinero tiene su versión; puede tener hasta 30 especias.</p>
<p><strong>Baharat (Líbano/Turquía):</strong> Pimienta negra + canela + comino + cilantro + clavo + nuez moscada + pimentón. Para cordero, arroz y quibbe.</p>

<h2>Conservación: el enemigo de las especias es la luz</h2>
<p>Las especias pierden hasta el 70% de su potencia aromática en 6 meses si se almacenan en recipientes transparentes cerca del fuego o la ventana. Guárdalas en frascos opacos o de vidrio oscuro, lejos del calor y la humedad. Si tus especias tienen más de un año, es mejor reemplazarlas.</p>
<p>Tuesta las especias enteras en sartén seca antes de molerlas: 1-2 minutos a fuego medio hasta que aromen. Eso libera los aceites esenciales y potencia el sabor exponencialmente.</p>',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],

            /* ─── 3 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Cómo Elegir, Comprar y Conservar el Pescado Fresco: Guía Definitiva',
                'slug'     => 'como-elegir-conservar-pescado-fresco',
                'category' => 'Técnicas',
                'excerpt'  => 'Saber si un pescado es fresco puede ser la diferencia entre un plato exquisito y uno mediocre. Aprende los indicadores que usan los chefs profesionales en el mercado, cómo conservarlo en casa y los errores que acortan su vida útil.',
                'featured_image' => 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=800&q=80',
                'image_alt' => 'Pescado fresco en mercado con hielo',
                'seo_title' => 'Cómo Elegir Pescado Fresco: Guía Completa del Chef',
                'seo_description' => 'Aprende a identificar pescado fresco con los 7 indicadores que usan los chefs. Técnicas de conservación, errores comunes y cómo alargar la vida útil en casa.',
                'content'  => '<h2>La frescura del pescado: por qué lo cambia todo</h2>
<p>A diferencia de las carnes rojas, que mejoran con la maduración controlada, el pescado es un producto en el que la frescura lo es prácticamente todo. Un pescado recién capturado tiene una textura firme, un sabor suave y marine, y una versatilidad total en la cocina. Uno de tres días mal conservado, aunque no esté "malo" en términos de seguridad alimentaria, pierde esa textura y desarrolla los sabores sulfurosos que muchas personas asocian con "olor a pescado".</p>
<p>La buena noticia es que identificar la frescura es una habilidad que se aprende en minutos y que transforma completamente tu relación con la pescadería.</p>

<h2>Los 7 indicadores de frescura que usan los chefs</h2>

<h3>1. Los ojos: el espejo de la frescura</h3>
<p>Los ojos de un pescado fresco son <strong>brillantes, convexos y con la pupila negra intensa</strong>. A medida que pasan las horas, los ojos se vuelven opacos, hundidos y la pupila se vuelve gris o lechosa. Es el indicador más rápido y confiable.</p>

<h3>2. Las branquias: color y olor</h3>
<p>Levanta la tapa branquial y observa el color. Las branquias frescas son de un <strong>rojo o rosa intenso, húmedas y brillantes</strong>. Con el tiempo se vuelven marrones, grises y secas. El olor debe ser marino, como el mar, no amoniacal ni putrefacto.</p>

<h3>3. La carne: firmeza y recuperación</h3>
<p>Presiona suavemente la carne con el dedo. En un pescado fresco, la <strong>carne recupera su forma inmediatamente</strong>. Si queda la marca del dedo hundida, el pescado lleva demasiado tiempo fuera del agua.</p>

<h3>4. Las escamas: adherencia</h3>
<p>Las escamas deben estar bien adheridas a la piel, brillantes y difíciles de remover. Si se caen con facilidad o la piel está arrugada, es una señal de que el pescado no es fresco.</p>

<h3>5. El olor: marino, no sulfuroso</h3>
<p>El pescado fresco huele a mar, a algas, a brisa oceánica. Nunca debe oler a "pescado fuerte". Ese olor característico que la gente asocia con el pescado es en realidad el olor de la descomposición, causado por bacterias que producen trimetilamina (TMA). Si notas ese olor, no lo compres.</p>

<h3>6. El vientre: sin hinchazón</h3>
<p>El vientre debe estar plano y firme. Un vientre hinchado indica que los gases de descomposición ya se están formando en el interior.</p>

<h3>7. La piel y el moco</h3>
<p>Un pescado fresco tiene una fina capa de moco transparente sobre la piel. Este moco se vuelve opaco y pegajoso con el tiempo. La piel debe tener colores vivos y brillantes, no apagados.</p>

<h2>Tipos de pescado y sus particularidades</h2>
<p><strong>Peces blancos (corvina, robalo, lenguado, tilapia):</strong> La carne debe ser blanca nacarada, nunca amarillenta. Son los más delicados y los que más rápido pierden calidad.</p>
<p><strong>Peces azules (atún, caballa, sardinas, salmon):</strong> La carne debe tener colores vivos y brillantes. El atún fresco es rojo intenso; si está marrón oscuro, ha oxidado.</p>
<p><strong>Mariscos y cefalópodos:</strong> Las conchas de mejillones y almejas deben estar cerradas. Si están abiertas y no se cierran al tocarlas, están muertas. Los calamares frescos tienen piel violácea brillante y carne blanca nacarada.</p>

<h2>Cómo conservar el pescado en casa</h2>

<h3>En refrigeración (hasta 2 días)</h3>
<p>El error más común: guardar el pescado en su empaque original o en una bolsa de plástico. Esto acumula humedad y acelera la descomposición.</p>
<p>La técnica correcta: coloca el pescado sobre una rejilla dentro de un recipiente, cúbrelo con papel absorbente y luego con film plástico. Guárdalo en la parte más fría del refrigerador (cerca del fondo, donde la temperatura es más estable). Cambia el papel absorbente si se empapa.</p>
<p>Aún mejor: usa el método <em>ikejime</em> japonés. Rodea el pescado de hielo picado en un colador dentro de un recipiente. El hielo derretido escurre y no empapa la carne. Cambia el hielo cada 12 horas.</p>

<h3>En congelación (hasta 3 meses)</h3>
<p>Para congelar sin que la carne pierda textura: envuelve el pescado en film plástico apretado, eliminando todo el aire, y luego en papel aluminio. Etiqueta con la fecha. El pescado pierde calidad principalmente por el "quemado de congelador" (oxidación por contacto con el aire).</p>
<p>Para descongelar correctamente: nunca a temperatura ambiente ni bajo agua caliente. Pasa el pescado del congelador al refrigerador la noche anterior. Si necesitas descongelar rápido, colócalo en una bolsa sellada bajo agua fría corriente.</p>

<h2>Cuánto dura el pescado según el tipo</h2>
<p><strong>Muy perecedero (1-2 días en frío):</strong> Lenguado, sardinas, anchoas, caballa.</p>
<p><strong>Moderadamente perecedero (2-3 días):</strong> Corvina, robalo, tilapia, trucha.</p>
<p><strong>Más estable (3-4 días):</strong> Salmón, atún, bacalao fresco.</p>
<p><strong>Congelado correctamente:</strong> Pescado blanco hasta 6 meses; pescado azul hasta 3 meses.</p>

<h2>La prueba de la cocción: el chef siempre sabe</h2>
<p>Un dato que pocos conocen: el pescado fresco cocinado al vapor o a la plancha huele a palomitas de maíz o mantequilla, nunca a "pescado fuerte". Si al cocinar tu pescado el olor es intensamente sulfuroso, la próxima vez cómpralo en otro sitio o en otro día de la semana.</p>',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(8),
            ],

            /* ─── 4 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'El Chocolate en México: De los Aztecas al Cacao de Oaxaca',
                'slug'     => 'historia-chocolate-mexico-aztecas-oaxaca',
                'category' => 'Historia & Cultura',
                'excerpt'  => 'El chocolate nació en México hace más de 3,000 años. Desde la bebida sagrada de los aztecas hasta el mole negro de Oaxaca, la historia del cacao mexicano es una de las más fascinantes de la gastronomía mundial.',
                'featured_image' => 'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?w=800&q=80',
                'image_alt' => 'Cacao mexicano y chocolate artesanal',
                'seo_title' => 'Historia del Chocolate en México: De los Aztecas a Hoy',
                'seo_description' => 'Descubre los orígenes del chocolate en México: el xocolatl azteca, el papel del cacao en las culturas mesoamericanas y el chocolate artesanal de Oaxaca hoy.',
                'content'  => '<h2>El regalo de Mesoamérica al mundo</h2>
<p>Antes de que existiera la tableta de chocolate con leche suiza, antes del pralinée belga y antes del ganache francés, existía el <em>xocolatl</em>. La bebida sagrada de los aztecas —amarga, espumosa, mezclada con chile y achiote— fue el origen de todo lo que hoy conocemos como chocolate.</p>
<p>México es la cuna del cacao. Las culturas olmeca, maya y azteca domesticaron el árbol <em>Theobroma cacao</em> hace más de 3,000 años en las selvas tropicales del golfo de México, el Soconusco (actual Chiapas) y la península de Yucatán. Lo que comenzó como una bebida ritual y medicinal se convirtió, siglos después, en el ingrediente más amado del mundo.</p>

<h2>El xocolatl: la bebida de los dioses</h2>
<p>Para los aztecas, el cacao era literalmente un regalo del dios Quetzalcóatl. Las semillas de cacao eran tan valiosas que funcionaban como moneda: diez granos compraban un conejo, cien granos podían adquirir un esclavo.</p>
<p>La bebida que preparaban era radicalmente diferente al chocolate que conocemos hoy. Las semillas de cacao fermentadas y secadas se tostaban en comal, se molían en metate de piedra y se mezclaban con agua fría. Luego se vertía la mezcla repetidamente de un recipiente a otro desde altura para crear una espuma gruesa. A esta base se le añadía chile rojo, achiote (para el color), vainilla y, a veces, flores de cacao.</p>
<p>No era dulce. Era amarga, especiada, astringente y completamente diferente a cualquier cosa que los europeos hubieran probado antes.</p>

<h2>El encuentro con Europa: el azúcar lo cambia todo</h2>
<p>Cuando Hernán Cortés llegó a Tenochtitlan en 1519, el emperor Moctezuma II lo recibió con jarras de xocolatl. Los conquistadores españoles, acostumbrados a los vinos y especias europeas, encontraron la bebida "amarga y sin gracia". Sin embargo, entendieron su valor económico y su poder estimulante.</p>
<p>Fueron los frailes españoles en los conventos de México quienes comenzaron la transformación. Alrededor de 1560, las monjas del Convento de los Ángeles en Puebla comenzaron a endulzar el cacao con azúcar de caña y a mezclarlo con canela y vainilla. Esa versión dulce fue la que llegó a la corte española y, desde allí, conquistó Europa entera.</p>

<h2>El mole negro: el chocolate que cocina</h2>
<p>Mientras Europa convertía el cacao en confitería, México siguió usándolo como ingrediente culinario. La mejor muestra de esta tradición es el mole negro de Oaxaca, una de las salsas más complejas del mundo.</p>
<p>El mole negro tradicional combina más de 30 ingredientes: varios tipos de chile (mulato, pasilla, chihuacle negro), chocolate artesanal de tableta, tomate, jitomate, plátano macho frito, pan tostado, tortilla quemada, almendras, cacahuates, pasas, especias (comino, pimienta, canela, clavo) y el indispensable chile negro quemado hasta el carbón, que le da su color característico.</p>
<p>El chocolate en el mole no aporta dulzor: actúa como cuerpo, como espesante y como modulador de la acidez de los chiles. Sin él, el mole sería plano y agresivo.</p>

<h2>Oaxaca: la capital mundial del chocolate artesanal</h2>
<p>Hoy, Oaxaca es el centro del movimiento <em>bean-to-bar</em> (del grano a la tableta) en México y uno de los referentes mundiales del cacao artesanal. La ciudad tiene molinillos tradicionales —pequeños talleres donde los clientes llevan sus mezclas personalizadas de cacao, azúcar, canela y almendras para ser molidas hasta convertirse en chocolate de tableta o en pasta para mole.</p>
<p>El cacao que se usa en Oaxaca es principalmente del tipo <em>criollo</em> y <em>trinitario</em>, de las regiones de Miahuatlán y la Sierra Sur. Estos cacaos tienen perfiles de sabor complejos con notas de frutas rojas, tabaco y flores, muy diferentes al cacao <em>forastero</em> de producción masiva.</p>

<h2>Cómo usar el chocolate mexicano en tu cocina</h2>
<p><strong>Para bebida tradicional:</strong> Disuelve 30 g de chocolate de tableta oaxaqueño (o Abuelita/Ibarra como alternativa) en 250 ml de leche o agua caliente. Bate con molinillo hasta crear espuma. Sirve con canela en polvo.</p>
<p><strong>En mole simplificado:</strong> Para empezar, prueba el mole negro en pasta que venden en los mercados. Deslíelo en caldo de pollo caliente, agrega chocolate de tableta rallado y ajusta con sal. Es una introducción honesta a los sabores del mole real.</p>
<p><strong>En postres:</strong> El chocolate de tableta mexicano, con su contenido de canela y azúcar de caña, funciona perfectamente en galletas, brownies y tartas, aportando un sabor cálido y especiado que el chocolate europeo no tiene.</p>

<h2>El futuro del cacao mexicano</h2>
<p>México produce solo el 1.5% del cacao mundial, pero tiene algunas de las variedades más preciadas por los chocolateros de alta gama. Organizaciones como el Consejo Nacional del Cacao trabajan para proteger los cacaos nativos mexicanos y mejorar las condiciones de los pequeños productores.</p>
<p>La próxima vez que pruebes un chocolate de origen mexicano —del Soconusco, de Oaxaca o de Tabasco— estarás probando el mismo sabor que movió el mundo hace 3,000 años. Solo que ahora está en tableta.</p>',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
            ],

            /* ─── 5 ─────────────────────────────────────────────────────── */
            [
                'title'    => 'Los Mejores Cortes de Carne para Asar a la Parrilla (y Cómo Cocinar Cada Uno)',
                'slug'     => 'mejores-cortes-carne-parrilla-como-cocinar',
                'category' => 'Técnicas',
                'excerpt'  => 'No todos los cortes se comportan igual en la parrilla. Conoce los 8 mejores cortes para asar, a qué temperatura y durante cuánto tiempo debe cocinar cada uno, y los secretos que separan un asado bueno de uno inolvidable.',
                'featured_image' => 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=800&q=80',
                'image_alt' => 'Cortes de carne a la parrilla con marcas de asado',
                'seo_title' => 'Los Mejores Cortes para Parrilla y Cómo Asarlos',
                'seo_description' => 'Guía completa de cortes para parrilla: entraña, picaña, bife de chorizo, costillas y más. Temperaturas, tiempos y técnicas para lograr el punto perfecto.',
                'content'  => '<h2>La parrilla como técnica y como ritual</h2>
<p>En Argentina, Uruguay, Colombia y Ecuador, asar a la parrilla no es solo cocinar: es un evento social, una declaración de identidad y, para muchos, una forma de arte. La diferencia entre un buen asado y uno memorable no es el equipo ni el fuego —es el conocimiento del corte.</p>
<p>Cada músculo del animal tiene una composición diferente de fibras, colágeno y grasa, y esa composición determina exactamente cómo debe cocinarse. El mismo error —asar una entraña como si fuera costilla, o una costilla como si fuera entraña— arruina el resultado final.</p>

<h2>Antes de empezar: los principios universales</h2>
<p><strong>La carne a temperatura ambiente:</strong> Saca los cortes del refrigerador al menos 30 minutos antes de asarlos. Una carne fría en el centro tarda más en cocinarse y se contrae de manera desigual.</p>
<p><strong>Sal gruesa, no fina:</strong> Usa sal gruesa o sal parrillera justo antes de poner la carne al fuego. La sal gruesa no penetra tan rápido como la fina y crea una costra exterior sin extraer la humedad interior.</p>
<p><strong>El fuego y sus zonas:</strong> Una buena parrilla tiene zonas de calor directo (brasas encendidas) y calor indirecto (sin brasas debajo). Los cortes delgados van al calor directo; los gruesos y los que necesitan cocción lenta van al indirecto.</p>

<h2>Los 8 mejores cortes y cómo cocinar cada uno</h2>

<h3>1. Entraña (Skirt Steak)</h3>
<p>La entraña es el diafragma del animal: un músculo largo, delgado y fibroso con una capa de grasa externa. Es posiblemente el corte más sabroso de la vaca, con un sabor profundo e intensamente bovino que no tienen los cortes "nobles".</p>
<p><strong>Cómo asarla:</strong> Fuego alto, calor directo. 3-4 minutos por cada lado para punto medio (rosa en el centro). Nunca la cocines más de well done: se vuelve dura y seca. Córtala siempre en contra de la fibra, en diagonal, para que los trozos sean tiernos al morder.</p>

<h3>2. Picaña (Rump Cap)</h3>
<p>La picaña es el corte estrella de Brasil. Es la cubierta superior de la cadera, con una generosa capa de grasa encima. En Argentina se llama "tapa de cuadril". Tiene un sabor intenso y una textura jugosa gracias a la grasa que se derrite durante la cocción.</p>
<p><strong>Cómo asarla:</strong> Pon la picaña entera con la grasa hacia arriba primero, en zona de calor indirecto, durante 20-25 minutos. Luego voltéala con la grasa hacia las brasas directas, 5-7 minutos, para dorar y crear una costra crujiente. Reposa 5 minutos antes de cortar en rodajas gruesas.</p>

<h3>3. Bife de Chorizo (New York Strip)</h3>
<p>Corte grueso, con grasa infiltrada y una franja de grasa lateral. Es uno de los cortes más equilibrados: lo suficientemente tierno para comerlo término medio, con suficiente grasa para tener sabor. El bife de chorizo es el corte que piden los que saben en Argentina.</p>
<p><strong>Cómo asarlo:</strong> Fuego alto directo, 4-5 minutos por lado para un bife de 3 cm. Usa termómetro: 54°C para rojo inglés, 60°C para a punto, 68°C para bien cocido. Sella también el borde de grasa poniéndolo de costado 1 minuto.</p>

<h3>4. Costillas de res</h3>
<p>Las costillas son el corte más paciente de la parrilla. El colágeno entre los huesos necesita tiempo y calor moderado para convertirse en gelatina, que es lo que hace que la carne se caiga del hueso con esa textura sedosa característica.</p>
<p><strong>Cómo asarlas:</strong> Cocción lenta en zona indirecta, 3-4 horas a fuego bajo (no hay prisa). Voltea cada 45 minutos. En la última media hora, muévelas al calor directo para crear la costra exterior. Si quieres acelerar, puedes precocinarlas 90 minutos en horno a 150°C antes de terminar en parrilla.</p>

<h3>5. T-bone y Porterhouse</h3>
<p>Estos cortes incluyen dos músculos separados por el hueso en T: el lomo (tiernísimo) y el bife de chorizo (más sabroso). El truco es que los dos músculos tienen diferentes puntos óptimos de cocción, lo que los hace técnicamente desafiantes.</p>
<p><strong>Cómo asarlos:</strong> Pon el hueso mirando hacia el centro de la parrilla (lejos del fuego más intenso). El lomo, al estar cerca del hueso, se cocina más lento. Cocina 4-5 minutos por lado para lograr que ambos queden a punto.</p>

<h3>6. Lomo (Tenderloin / Filete)</h3>
<p>El lomo es el músculo más tierno del animal porque prácticamente no trabaja. Por eso es el corte más caro. Sin embargo, tiene menos sabor que los cortes con más grasa y colágeno. Se compensa con una buena salsa o mantequilla compuesta.</p>
<p><strong>Cómo asarlo:</strong> Fuego muy alto, 2-3 minutos por lado para sellado exterior, luego zona indirecta hasta alcanzar 54-56°C en el centro. El lomo se seca rápido: nunca lo cocines más allá del punto a punto.</p>

<h3>7. Chorizo y morcilla</h3>
<p>Aunque no son cortes propiamente, son los compañeros inseparables del asado latinoamericano. El chorizo necesita cocción a fuego medio durante 15-20 minutos, girándolo cada 5 minutos para que se cocine de manera uniforme sin reventar. La morcilla es más delicada: fuego bajo, sin pinchar, hasta que esté caliente por dentro.</p>

<h3>8. Vacío (Flank Steak)</h3>
<p>Corte plano de la pared abdominal, con fibras largas y sabor intenso. Muy popular en Argentina. Necesita marinada o una buena cantidad de grasa infiltrada para no quedar seco.</p>
<p><strong>Cómo asarlo:</strong> Fuego medio-alto, con la capa de grasa hacia arriba primero para que se derrita sobre la carne. 6-7 minutos por lado. Cortar en diagonal al grano en trozos finos.</p>

<h2>El reposo: el paso que todos se saltan</h2>
<p>Cuando sacas la carne del fuego, las fibras musculares están contraídas por el calor y los jugos están concentrados en el centro. Si cortas en ese momento, los jugos saldrán en el plato y la carne quedará seca. Deja reposar la carne cubierta con papel aluminio durante la mitad del tiempo de cocción (si asaste 10 minutos, reposa 5). Los jugos se redistribuyen y cada bocado queda jugoso.</p>

<h2>El termómetro: el instrumento que cambia todo</h2>
<p>Un termómetro de cocina de lectura instantánea (unos $15-25 USD) elimina completamente el factor azar del asado. Las temperaturas internas son: rojo inglés (rare) 48-52°C, a punto (medium-rare) 54-57°C, medio (medium) 60-63°C, bien cocido (well-done) 71°C+. Nunca cocines un corte de calidad hasta well done: es como pedir un vino caro y mezclarlo con refresco.</p>',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
            ],

        ];

        foreach ($posts as $data) {
            Post::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command->info('✅  BlogArticlesSeeder: 5 artículos creados/actualizados.');
    }
}
