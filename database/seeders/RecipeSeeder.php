<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeFaq;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@marvinbaptista.com')->first();

        // Get or create tags
        $tags = collect([
            'Sin Gluten', 'Vegetariano', 'Rápido', 'Tradicional', 'Familiar',
            'Para Compartir', 'Picante', 'Sin Lactosa', 'Mar y Tierra', 'Festivo',
        ])->mapWithKeys(fn($name) => [
            $name => Tag::firstOrCreate(['name' => $name])
        ]);

        $recipes = [

            // ─────────────────────────────────────────────────────────────────
            // 1. SECO DE POLLO ECUATORIANO
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Seco de Pollo Ecuatoriano',
                    'subtitle'         => 'El guiso más querido de la cocina ecuatoriana',
                    'description'      => 'El seco de pollo es uno de los platos más emblemáticos de Ecuador. Un guiso de pollo tierno bañado en una salsa de chicha de jora, naranjilla y especias que llena el hogar de aromas inconfundibles. Ideal para reuniones familiares.',
                    'story'            => 'Este plato tiene raíces profundas en la sierra ecuatoriana, donde las familias lo preparan cada domingo. La palabra "seco" no significa que el plato sea seco, sino que proviene de una reducción de la salsa hasta obtener una consistencia perfecta. En casa, mi abuela lo servía siempre con arroz blanco y tajadas de maduro frito, y ese sabor es el que intento recrear en cada preparación.',
                    'tips_secrets'     => "• Marinar el pollo con el aliño criollo al menos 2 horas antes de cocinar para sabores más profundos.\n• La chicha de jora es el secreto auténtico; si no encuentras, usa cerveza negra.\n• Cocina a fuego medio para que la salsa reduzca sin pegarse.\n• El cilantro fresco al final es imprescindible, nunca lo omitas.\n• Sirve con arroz blanco y aguacate para una experiencia completa.",
                    'origin_country'   => 'Ecuador',
                    'origin_region'    => 'Sierra',
                    'prep_time_minutes'=> 20,
                    'cook_time_minutes'=> 45,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'easy',
                    'seo_title'        => 'Seco de Pollo Ecuatoriano Auténtico | Receta Tradicional Paso a Paso',
                    'seo_description'  => 'Aprende a preparar el auténtico seco de pollo ecuatoriano con chicha de jora. Receta tradicional de la sierra ecuatoriana, tierno y lleno de sabor. ¡Fácil y delicioso!',
                    'schema_rating_value' => 4.8,
                    'schema_rating_count' => 124,
                    'view_count'       => 3840,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(30),
                ],
                'category' => 'Recetas Ecuatorianas',
                'tags'     => ['Tradicional', 'Familiar', 'Para Compartir'],
                'ingredients' => [
                    ['group' => 'Pollo', 'items' => [
                        [1.5, 'kg', 'pollo troceado (con hueso)', null],
                        [3, 'dientes', 'ajo', null],
                        [1, 'cdta', 'comino molido', null],
                        [1, 'cdta', 'orégano seco', null],
                        [2, 'cdas', 'aceite de achiote', 'o aceite vegetal con color'],
                        [1, '', 'sal y pimienta', 'al gusto'],
                    ]],
                    ['group' => 'Salsa', 'items' => [
                        [2, '', 'cebollas blancas medianas', 'picadas finamente'],
                        [4, '', 'tomates maduros', 'picados'],
                        [2, '', 'pimientos verdes', 'picados'],
                        [1, 'taza', 'chicha de jora', 'o cerveza negra'],
                        [0.5, 'taza', 'jugo de naranjilla', 'o naranja agria'],
                        [1, 'manojo', 'cilantro fresco', 'picado'],
                        [2, 'cdas', 'salsa de tomate', null],
                        [1, 'cdta', 'azúcar', 'para equilibrar la acidez'],
                    ]],
                    ['group' => 'Para Servir', 'items' => [
                        [2, 'tazas', 'arroz blanco cocido', null],
                        [1, '', 'aguacate', 'en tajadas'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar el aliño', 'Mezcla el ajo machacado, comino, orégano, sal y pimienta. Masajea el pollo con esta mezcla y el aceite de achiote. Deja marinar al menos 30 minutos (mejor 2 horas en refrigerador).', 10],
                    [2, 'Sofreír el pollo', 'En una olla grande a fuego alto, dora los trozos de pollo por todos lados hasta que estén bien sellados (5-6 min). Retira y reserva.', 8],
                    [3, 'Preparar el refrito', 'En la misma olla, sofríe la cebolla y el pimiento a fuego medio durante 8 minutos hasta que estén tiernos. Agrega el tomate y cocina 5 minutos más.', 13],
                    [4, 'Agregar líquidos', 'Incorpora la chicha de jora, el jugo de naranjilla y la salsa de tomate. Mezcla bien, prueba de sal. Vuelve a poner el pollo en la olla.', 2],
                    [5, 'Cocinar a fuego lento', 'Tapa y cocina a fuego medio-bajo por 30-35 minutos, revisando ocasionalmente. La salsa debe reducir y el pollo quedar muy tierno. Agrega el cilantro los últimos 5 minutos.', 35],
                    [6, 'Servir', 'Sirve el seco de pollo con arroz blanco, tajadas de aguacate y más cilantro fresco picado encima.', 5],
                ],
                'faqs' => [
                    ['¿Puedo usar chicha de jora sin alcohol?', 'Sí, existe chicha de jora sin fermentar, o puedes sustituirla por caldo de pollo con una cucharadita de vinagre de manzana para aportar acidez similar.'],
                    ['¿Cuánto tiempo se conserva el seco de pollo?', 'En refrigerador bien tapado dura hasta 4 días. También se congela perfectamente hasta 3 meses; descongela en refrigerador la noche anterior.'],
                    ['¿Puedo hacerlo con pollo sin hueso?', 'Sí, aunque el hueso aporta más sabor a la salsa. Si usas pechuga sin hueso, reduce el tiempo de cocción a 20-25 minutos para que no quede seca.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 2. CEVICHE DE CAMARÓN ECUATORIANO
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Ceviche de Camarón Ecuatoriano',
                    'subtitle'         => 'Fresco, ácido y lleno de sabor del Pacífico',
                    'description'      => 'El ceviche de camarón ecuatoriano es diferente al peruano: el camarón va cocido y bañado en una salsa de tomate, limón y naranja agria. Refrescante, fácil de preparar y perfecto para cualquier ocasión.',
                    'story'            => 'En las costas de Manabí y Esmeraldas, el ceviche es prácticamente una religión. A diferencia del ceviche peruano que "cocina" el pescado crudo en limón, el ecuatoriano usa camarón ya cocido con una salsa vibrante a base de tomate. Lo recuerdo comido en un plato de barro frente al mar, con patacones crujientes y una cerveza fría. Esa imagen siempre me motiva a cocinarlo.',
                    'tips_secrets'     => "• No sobrecocines los camarones, 2-3 minutos es suficiente para que queden jugosos.\n• La naranja agria (o la mezcla limón+naranja) es el alma de la salsa.\n• Dejar reposar 15 minutos antes de servir permite que los sabores se integren.\n• Los patacones (tostones) son el acompañamiento clásico e imprescindible.\n• Agrega unas gotas de picante para potenciar el sabor.",
                    'origin_country'   => 'Ecuador',
                    'origin_region'    => 'Costa',
                    'prep_time_minutes'=> 15,
                    'cook_time_minutes'=> 10,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'easy',
                    'seo_title'        => 'Ceviche de Camarón Ecuatoriano Auténtico | Receta Fácil y Rápida',
                    'seo_description'  => 'Prepara el auténtico ceviche de camarón ecuatoriano con salsa de tomate, limón y naranja agria. Fresco, delicioso y listo en 25 minutos. ¡La receta más buscada!',
                    'schema_rating_value' => 4.9,
                    'schema_rating_count' => 287,
                    'view_count'       => 6120,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(25),
                ],
                'category' => 'Recetas Ecuatorianas',
                'tags'     => ['Rápido', 'Sin Gluten', 'Mar y Tierra'],
                'ingredients' => [
                    ['group' => 'Ceviche', 'items' => [
                        [500, 'g', 'camarones pelados y limpios', 'frescos o descongelados'],
                        [4, '', 'tomates pera maduros', 'picados en cubos pequeños'],
                        [1, '', 'cebolla paiteña grande', 'en juliana fina'],
                        [4, 'cdas', 'jugo de limón fresco', null],
                        [4, 'cdas', 'jugo de naranja agria', 'o 2 cdas limón + 2 cdas naranja'],
                        [2, 'cdas', 'ketchup o salsa de tomate', null],
                        [0.25, 'taza', 'cilantro fresco', 'picado'],
                        [1, 'cdta', 'salsa inglesa', null],
                        [1, '', 'ají o chile', 'picado finamente (opcional)'],
                        [1, '', 'sal y pimienta', 'al gusto'],
                    ]],
                    ['group' => 'Para Servir', 'items' => [
                        [8, '', 'patacones o tostones', 'recién fritos'],
                        [1, 'bolsa', 'chifles (chips de plátano)', null],
                        [4, '', 'rodajas de limón', null],
                    ]],
                ],
                'steps' => [
                    [1, 'Cocinar camarones', 'Lleva agua con sal y un chorrito de limón a hervir. Agrega los camarones y cocina exactamente 2-3 minutos hasta que estén rosados. Retira inmediatamente y enfría en agua con hielo para detener la cocción.', 5],
                    [2, 'Curtir la cebolla', 'Lava la cebolla en juliana con agua fría por 2 minutos para quitarle el amargor. Escurre bien y mezcla con el jugo de limón, sal y una pizca de azúcar. Deja reposar 10 minutos.', 12],
                    [3, 'Preparar la salsa', 'En un tazón grande mezcla el tomate picado, el jugo de naranja agria, el ketchup y la salsa inglesa. Prueba de sal y ajusta la acidez al gusto.', 5],
                    [4, 'Integrar todo', 'Agrega los camarones escurridos y la cebolla curtida a la salsa de tomate. Incorpora el cilantro y el ají si lo usas. Revuelve suavemente.', 3],
                    [5, 'Reposar y servir', 'Refrigera 15 minutos para que los sabores se integren. Sirve frío en vasos o platos, con patacones y chifles a un lado, y una rodaja de limón.', 15],
                ],
                'faqs' => [
                    ['¿Cuál es la diferencia entre el ceviche ecuatoriano y el peruano?', 'El ecuatoriano usa camarón cocido con salsa de tomate caliente o fría, mientras el peruano usa pescado crudo "cocido" solo con leche de tigre (limón + ají). Son dos platos distintos con el mismo nombre.'],
                    ['¿Puedo prepararlo con anticipación?', 'Puedes preparar la salsa y cocinar los camarones con hasta 4 horas de anticipación. Mezcla todo 30 minutos antes de servir para mejor textura.'],
                    ['¿Se puede hacer con langostinos o cangrejo?', 'Absolutamente. Cualquier marisco funciona bien. Con langostinos el resultado es especialmente delicioso.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 3. LLAPINGACHOS
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Llapingachos con Chorizo y Huevo',
                    'subtitle'         => 'Tortitas de papa con queso, el desayuno ecuatoriano por excelencia',
                    'description'      => 'Los llapingachos son tortitas de puré de papa rellenas de queso fresco, doradas en manteca de chancho. Un plato de la sierra ecuatoriana que se sirve en desayunos y almuerzos con huevo, chorizo y salsa de maní.',
                    'story'            => 'En Ambato, ciudad conocida como "la tierra de los llapingachos", este plato es toda una institución. Cada familia tiene su receta secreta, pero el alma siempre es la misma: papa chola, queso fresco y mucho amor. Mi versión incluye la salsa de maní que aprendí de una señora en el mercado de Riobamba, quien me dijo que el secreto está en tostar el maní antes de molerlo.',
                    'tips_secrets'     => "• Usa papa chola o papa gabriela, son las que mejor textura dan al llapingacho.\n• El puré debe quedar bien seco antes de formar las tortitas, o se romperán en la sartén.\n• Cocinar en manteca de cerdo (o mantequilla) da el sabor auténtico.\n• La salsa de maní es imprescindible, nunca los sirvas sin ella.\n• Para que no se abran, refrigera 20 minutos antes de freír.",
                    'origin_country'   => 'Ecuador',
                    'origin_region'    => 'Sierra (Ambato)',
                    'prep_time_minutes'=> 30,
                    'cook_time_minutes'=> 20,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'medium',
                    'seo_title'        => 'Llapingachos Ecuatorianos Auténticos | Receta con Salsa de Maní',
                    'seo_description'  => 'Receta auténtica de llapingachos ecuatorianos: tortitas de papa con queso doradas en manteca, servidas con salsa de maní, chorizo y huevo frito. Paso a paso.',
                    'schema_rating_value' => 4.7,
                    'schema_rating_count' => 93,
                    'view_count'       => 2780,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(20),
                ],
                'category' => 'Recetas Ecuatorianas',
                'tags'     => ['Vegetariano', 'Tradicional', 'Familiar'],
                'ingredients' => [
                    ['group' => 'Llapingachos', 'items' => [
                        [1, 'kg', 'papa chola', 'cocida y pelada'],
                        [200, 'g', 'queso fresco ecuatoriano', 'desmenuzado'],
                        [2, 'cdas', 'mantequilla', null],
                        [0.5, '', 'cebolla blanca', 'finamente picada'],
                        [1, 'cdta', 'achiote', 'en polvo o en pasta'],
                        [1, '', 'sal y comino', 'al gusto'],
                        [2, 'cdas', 'manteca de cerdo', 'para freír (o mantequilla)'],
                    ]],
                    ['group' => 'Salsa de Maní', 'items' => [
                        [200, 'g', 'maní tostado y pelado', null],
                        [2, 'tazas', 'leche entera', null],
                        [0.5, '', 'cebolla blanca', 'finamente picada'],
                        [1, 'diente', 'ajo', null],
                        [1, 'cdta', 'achiote', null],
                        [1, '', 'sal y pimienta', 'al gusto'],
                    ]],
                    ['group' => 'Para Servir', 'items' => [
                        [4, '', 'huevos', 'fritos o revueltos'],
                        [4, '', 'chorizos ecuatorianos', 'a la plancha'],
                        [2, '', 'tomates', 'en rodajas'],
                        [1, '', 'aguacate', 'en tajadas'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar el puré', 'Aplasta las papas cocidas mientras aún están calientes hasta obtener un puré sin grumos. Agrega la mantequilla, comino y sal. El puré debe quedar firme, no cremoso. Deja enfriar 10 minutos.', 15],
                    [2, 'Sofreír la cebolla', 'En una sartén con el achiote sofríe la cebolla picada hasta que esté dorada y suave. Mezcla con el puré de papa.', 8],
                    [3, 'Formar y rellenar', 'Toma porciones de puré del tamaño de una pelota de golf. Aplana en la palma, pon queso desmenuzado en el centro, cierra y forma una tortita redonda de 1.5 cm de grosor. Refrigera 20 minutos.', 25],
                    [4, 'Preparar salsa de maní', 'Licúa el maní tostado con la leche hasta obtener una crema. En una sartén sofríe la cebolla y el ajo con achiote, agrega la crema de maní, sal y cocina 5 minutos revolviendo hasta espesar.', 10],
                    [5, 'Dorar llapingachos', 'Calienta la manteca a fuego medio. Cocina los llapingachos 3-4 minutos por lado hasta que estén bien dorados y crujientes por fuera.', 10],
                    [6, 'Montar el plato', 'Sirve 2-3 llapingachos por plato, napa con salsa de maní caliente, acompaña con huevo frito, chorizo a la plancha, tomate y aguacate.', 5],
                ],
                'faqs' => [
                    ['¿Puedo hacer los llapingachos sin queso para que sean veganos?', 'Sí, puedes omitir el queso y usar margarina vegetal. El resultado es diferente pero igualmente delicioso. La salsa de maní también puedes hacerla con leche vegetal.'],
                    ['¿Por qué se me rompen los llapingachos al freírlos?', 'Principalmente porque el puré tiene demasiada humedad o están muy fríos al entrar al aceite. Asegúrate de que el puré esté bien seco y los llapingachos a temperatura ambiente antes de freír.'],
                    ['¿Se pueden congelar?', 'Sí, congélalos ya formados pero sin freír, sobre una bandeja separados. Cuando los necesites, descongela en refrigerador y fríe directamente.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 4. TACOS DE CARNITAS MEXICANOS
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Tacos de Carnitas Mexicanos',
                    'subtitle'         => 'Cerdo confitado en su propia manteca, irresistible',
                    'description'      => 'Las carnitas son la cumbre de la cocina michoacana: cerdo cocinado lentamente en su propia grasa hasta quedar tierno por dentro y crujiente por fuera. Servidas en tortillas de maíz con cebolla, cilantro y salsa verde, son perfectas para reuniones.',
                    'story'            => 'En Morelia, la capital de Michoacán, las carnitas se venden en enormes cazos de cobre donde borbotea la manteca desde las 6 de la mañana. El olor se extiende por todo el mercado y es imposible resistirse. Esta receta adapta el método tradicional al horno casero para que cualquiera pueda replicar ese sabor auténtico sin necesidad de un cazo de cobre ni litros de manteca.',
                    'tips_secrets'     => "• La clave es cocinar a baja temperatura largo tiempo: 160°C por 3 horas.\n• El jugo de naranja y la leche condensada son los secretos de las carnitas michoacanas.\n• Al final, sube la temperatura a 220°C para que queden crujientes.\n• Usa tortillas de maíz, nunca de harina, para el auténtico sabor.\n• La salsa verde y el guacamole son insustituibles.",
                    'origin_country'   => 'México',
                    'origin_region'    => 'Michoacán',
                    'prep_time_minutes'=> 15,
                    'cook_time_minutes'=> 180,
                    'servings'         => 6,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'medium',
                    'seo_title'        => 'Tacos de Carnitas Auténticos Estilo Michoacán | Receta Paso a Paso',
                    'seo_description'  => 'Receta auténtica de tacos de carnitas mexicanas estilo Michoacán. Cerdo tierno y crujiente cocinado lentamente. ¡El secreto está en el jugo de naranja y la manteca!',
                    'schema_rating_value' => 4.9,
                    'schema_rating_count' => 341,
                    'view_count'       => 8930,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(18),
                ],
                'category' => 'Recetas Latinoamericanas',
                'tags'     => ['Tradicional', 'Para Compartir', 'Festivo', 'Sin Gluten'],
                'ingredients' => [
                    ['group' => 'Carnitas', 'items' => [
                        [1.5, 'kg', 'paleta o pierna de cerdo', 'con hueso, en trozos grandes'],
                        [1, 'taza', 'jugo de naranja fresco', null],
                        [2, 'cdas', 'leche condensada', 'el secreto michoacano'],
                        [6, 'dientes', 'ajo', null],
                        [2, '', 'hojas de laurel', null],
                        [1, 'cdta', 'comino molido', null],
                        [1, 'cdta', 'orégano seco mexicano', null],
                        [1, '', 'sal y pimienta negra', 'al gusto'],
                        [4, 'cdas', 'manteca de cerdo', 'o aceite vegetal'],
                    ]],
                    ['group' => 'Para los Tacos', 'items' => [
                        [24, '', 'tortillas de maíz pequeñas', 'calientes'],
                        [1, '', 'cebolla blanca', 'finamente picada'],
                        [1, 'manojo', 'cilantro fresco', 'picado'],
                        [3, '', 'limas o limones', 'en cuartos'],
                        [2, '', 'aguacates', 'para guacamole'],
                        [1, 'taza', 'salsa verde', 'tomatillo'],
                    ]],
                ],
                'steps' => [
                    [1, 'Marinar', 'Mezcla el jugo de naranja, leche condensada, ajo machacado, comino, orégano, sal y pimienta. Vierte sobre el cerdo y masajea bien. Deja marinar mínimo 1 hora (mejor toda la noche).', 10],
                    [2, 'Precalentar y sellar', 'Precalienta el horno a 160°C. En una olla apta para horno con la manteca caliente, sella los trozos de cerdo a fuego alto hasta dorarlos por todos lados (3-4 min por lado).', 15],
                    [3, 'Hornear lento', 'Agrega la marinada, el laurel y 0.5 taza de agua. Cubre herméticamente con papel aluminio y hornea a 160°C por 2.5-3 horas hasta que la carne se desmenuce con un tenedor.', 180],
                    [4, 'Desmenuzar y crujir', 'Retira del horno, desmenúzala en trozos medianos. Regresa al horno destapado a 220°C por 15-20 minutos hasta que los bordes estén crujientes y dorados.', 20],
                    [5, 'Armar los tacos', 'Calienta las tortillas en comal. Coloca carnitas, cebolla picada, cilantro. Exprime limón y agrega salsa verde y guacamole al gusto.', 10],
                ],
                'faqs' => [
                    ['¿Puedo hacer carnitas en olla de presión?', 'Sí, cocina a presión por 45-50 minutos. Luego desmenúzalas y dóralas al horno o en sartén para conseguir el exterior crujiente característico.'],
                    ['¿Con qué parte del cerdo quedan mejor?', 'La paleta (espaldilla) es la más tradicional por su contenido de grasa y colágeno. La pierna también funciona bien. Evita partes muy magras como el lomo, que quedan secas.'],
                    ['¿Las carnitas se pueden preparar con anticipación?', 'Son perfectas para preparar el día anterior. Guarda la carne en su jugo en el refrigerador y dórala justo antes de servir para que queden crujientes.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 5. PASTA CARBONARA AUTÉNTICA
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Pasta Carbonara Auténtica Romana',
                    'subtitle'         => 'Sin crema de leche, sin cebolla: la receta original de Roma',
                    'description'      => 'La verdadera carbonara romana lleva solo huevo, queso Pecorino Romano, guanciale y pimienta negra. Sin crema, sin cebolla, sin ajo. Una de las salsas de pasta más perfectas del mundo cuando se hace bien.',
                    'story'            => 'Pocos platos generan tanto debate como la carbonara. En Roma, los puristas se horrorizan al ver recetas con crema de leche o cebolla. Tuve el privilegio de aprender la técnica correcta de un trattoria en el barrio del Trastevere, donde el truco está en emulsionar el huevo con el calor del pasta, nunca directamente al fuego. Una vez dominas eso, ya no volverás a la carbonara con crema.',
                    'tips_secrets'     => "• NUNCA agregues crema de leche: la cremosidad viene del huevo y el queso.\n• El guanciale (carrillada curada) es el ingrediente original; la panceta es el sustituto.\n• La pasta debe estar recién escurrida y muy caliente para emulsionar la salsa.\n• Usa agua de cocción de la pasta para regular la consistencia.\n• Pimienta negra recién molida y generosa es fundamental.",
                    'origin_country'   => 'Italia',
                    'origin_region'    => 'Roma',
                    'prep_time_minutes'=> 10,
                    'cook_time_minutes'=> 20,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'medium',
                    'seo_title'        => 'Pasta Carbonara Auténtica Romana Sin Crema | Receta Original Italiana',
                    'seo_description'  => 'Receta auténtica de pasta carbonara romana: solo huevo, Pecorino Romano, guanciale y pimienta. Sin crema de leche. El método perfecto para una carbonara cremosa.',
                    'schema_rating_value' => 4.8,
                    'schema_rating_count' => 512,
                    'view_count'       => 12400,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(15),
                ],
                'category' => 'Recetas Mediterráneas',
                'tags'     => ['Rápido', 'Tradicional'],
                'ingredients' => [
                    ['group' => 'Carbonara', 'items' => [
                        [400, 'g', 'spaghetti o rigatoni', null],
                        [200, 'g', 'guanciale', 'o panceta en cubos pequeños'],
                        [4, '', 'yemas de huevo', 'a temperatura ambiente'],
                        [1, '', 'huevo entero', 'a temperatura ambiente'],
                        [100, 'g', 'Pecorino Romano', 'recién rallado'],
                        [50, 'g', 'Parmigiano Reggiano', 'recién rallado'],
                        [1, 'cdta', 'pimienta negra', 'recién molida, generosa'],
                        [1, '', 'sal', 'solo para el agua de la pasta'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar la salsa de huevo', 'En un tazón, mezcla enérgicamente las yemas con el huevo entero y los quesos rallados. Agrega pimienta negra generosa. Reserva.', 5],
                    [2, 'Cocinar el guanciale', 'En una sartén grande sin aceite, cocina el guanciale a fuego medio-bajo hasta que esté crujiente y haya soltado su grasa. Retira del fuego, reserva la grasa en la sartén.', 8],
                    [3, 'Cocer la pasta', 'Hierve la pasta en agua muy salada (como el mar) al dente. Reserva 1 taza del agua de cocción antes de escurrir.', 10],
                    [4, 'Crear la emulsión', 'Con el fuego apagado, agrega la pasta escurrida a la sartén con la grasa del guanciale. Añade 2-3 cdas del agua de pasta a la mezcla de huevo para templarla. Vierte la salsa sobre la pasta y revuelve vigorosamente. Agrega agua de pasta poco a poco hasta lograr una salsa cremosa.', 5],
                    [5, 'Servir de inmediato', 'Agrega el guanciale crujiente, más pimienta negra y queso rallado. Sirve inmediatamente en platos calientes.', 2],
                ],
                'faqs' => [
                    ['¿Por qué se me cuajan los huevos en la carbonara?', 'Porque el fuego estaba demasiado alto. La pasta debe estar fuera del fuego al mezclar con los huevos. El calor residual es suficiente para emulsionar.'],
                    ['¿Puedo sustituir el guanciale?', 'La panceta ahumada es el sustituto más común y delicioso. El bacon también funciona aunque cambia el perfil de sabor.'],
                    ['¿Se puede hacer carbonara vegetariana?', 'Sí, sustituye el guanciale por champiñones dorados o tomates secos. El resultado es diferente pero igualmente cremoso.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 6. GUACAMOLE CLÁSICO
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Guacamole Clásico Mexicano',
                    'subtitle'         => 'Cremoso, fresco y listo en 10 minutos',
                    'description'      => 'El guacamole auténtico no lleva mayonesa ni crema. Solo aguacate maduro, lima, cilantro, cebolla y jalapeño. Sencillo, brillante y perfecto para cualquier ocasión.',
                    'story'            => 'El guacamole tiene más de 500 años de historia: los aztecas ya lo preparaban con aguacate y chile. La versión moderna que conocemos viene de Jalisco y Michoacán, donde los aguacates Hass crecen en las condiciones perfectas. Lo mejor del guacamole es su honestidad: no hay donde esconderse, la calidad del aguacate lo es todo.',
                    'tips_secrets'     => "• El aguacate DEBE estar perfectamente maduro: cede al presionar suavemente.\n• El cilantro fresco es negociable, pero el limón no.\n• Agrega el jalapeño poco a poco y prueba el picante.\n• Para evitar que se oxide, pon el hueso dentro y cúbrelo con plástico a contacto.\n• No uses licuadora: el guacamole debe tener textura.",
                    'origin_country'   => 'México',
                    'origin_region'    => 'Jalisco',
                    'prep_time_minutes'=> 10,
                    'cook_time_minutes'=> 0,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'easy',
                    'seo_title'        => 'Guacamole Auténtico Mexicano | Receta Clásica en 10 Minutos',
                    'seo_description'  => 'Guacamole auténtico mexicano sin mayonesa: solo aguacate Hass, lima, cilantro y jalapeño. La receta original lista en 10 minutos. ¡El mejor dip del mundo!',
                    'schema_rating_value' => 4.9,
                    'schema_rating_count' => 628,
                    'view_count'       => 15200,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(12),
                ],
                'category' => 'Recetas Latinoamericanas',
                'tags'     => ['Rápido', 'Vegetariano', 'Sin Gluten', 'Sin Lactosa'],
                'ingredients' => [
                    ['group' => 'Guacamole', 'items' => [
                        [3, '', 'aguacates Hass maduros', null],
                        [2, '', 'limas (o limones)', 'el jugo'],
                        [0.5, '', 'cebolla blanca', 'finamente picada'],
                        [2, '', 'jalapeños o chiles serranos', 'sin semillas, picados'],
                        [0.5, 'taza', 'cilantro fresco', 'picado (sin tallos gruesos)'],
                        [1, '', 'tomate pera maduro', 'sin semillas, en cubos'],
                        [1, 'cdta', 'sal de mar', 'al gusto'],
                    ]],
                    ['group' => 'Para Servir', 'items' => [
                        [1, 'bolsa', 'totopos o nachos', null],
                        [1, '', 'tostadas de maíz', 'opcional'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar ingredientes', 'Pica finamente la cebolla, el jalapeño, el tomate y el cilantro. Exprime las limas. Ten todos los ingredientes listos antes de abrir el aguacate para evitar la oxidación.', 5],
                    [2, 'Aplastar el aguacate', 'Corta los aguacates a la mitad, retira el hueso. Extrae la pulpa con una cuchara y colócala en un molcajete o tazón. Agrega la sal y aplasta con un tenedor hasta la textura deseada (con algunos trozos para rusticidad).', 3],
                    [3, 'Integrar y sazonar', 'Agrega el jugo de lima de inmediato para evitar oxidación. Incorpora la cebolla, jalapeño, tomate y cilantro. Mezcla suavemente y prueba de sal y picante. Ajusta según tu preferencia.', 2],
                    [4, 'Servir', 'Sirve de inmediato con totopos. Si no consumes de inmediato, cubre con plástico a contacto directo con el guacamole (sin aire) y el hueso dentro para que no se oxide.', 1],
                ],
                'faqs' => [
                    ['¿Cómo evito que el guacamole se ponga negro?', 'El jugo de lima ayuda mucho. Para guardarlo, coloca el hueso dentro, cúbrelo con plástico a contacto directo (sin burbujas de aire) y refrigera. Aguanta bien hasta 24 horas.'],
                    ['¿Qué aguacate es mejor para guacamole?', 'El aguacate Hass es el preferido por su textura cremosa y sabor mantecoso. Reconoce uno maduro porque la cáscara es oscura (casi negra) y cede ligeramente al presionar.'],
                    ['¿Puedo congelarlo?', 'Sí, se congela sorprendentemente bien. Usa bolsas zip eliminando todo el aire. Al descongelar puede quedar un poco aguado; mezcla bien y queda casi como recién hecho.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 7. CEVICHE PERUANO
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Ceviche Peruano de Corvina con Leche de Tigre',
                    'subtitle'         => 'El plato bandera del Perú, con la técnica perfecta',
                    'description'      => 'El ceviche peruano es elegante en su simpleza: corvina fresca "cocida" únicamente con limón, ají amarillo, cilantro y cebolla roja. La leche de tigre —el jugo resultante— es la joya del plato, usada como shot digestivo.',
                    'story'            => 'Lima es la capital gastronómica de Sudamérica, y el ceviche es su rey. A diferencia de interpretaciones suavizadas, el ceviche limeño auténtico es vibrante, ácido y poderoso. El ají amarillo es insustituible: le da ese color dorado y un picante frutal único que no tiene equivalente en ningún otro chile del mundo.',
                    'tips_secrets'     => "• El pescado DEBE ser fresquísimo, de confianza. Si no, usa camarón cocido.\n• El limón peruano (más ácido que el mexicano) es fundamental.\n• El tiempo de 'cocción' en limón: máximo 10-15 minutos para corvina.\n• La cebolla roja en juliana muy fina, lavada en agua fría para suavizarla.\n• Sirve inmediatamente: el ceviche espera a nadie.",
                    'origin_country'   => 'Perú',
                    'origin_region'    => 'Lima',
                    'prep_time_minutes'=> 20,
                    'cook_time_minutes'=> 15,
                    'servings'         => 4,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'medium',
                    'seo_title'        => 'Ceviche Peruano Auténtico de Corvina | Receta con Leche de Tigre',
                    'seo_description'  => 'Receta auténtica de ceviche peruano de corvina con ají amarillo y leche de tigre. Aprende la técnica correcta del ceviche limeño paso a paso. ¡El plato bandera del Perú!',
                    'schema_rating_value' => 4.9,
                    'schema_rating_count' => 198,
                    'view_count'       => 5680,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(10),
                ],
                'category' => 'Recetas Latinoamericanas',
                'tags'     => ['Sin Gluten', 'Rápido', 'Sin Lactosa'],
                'ingredients' => [
                    ['group' => 'Ceviche', 'items' => [
                        [600, 'g', 'corvina o lenguado fresco', 'en cubos de 2cm'],
                        [12, '', 'limones peruanos (o 8 limones verdes)', 'solo el jugo'],
                        [2, '', 'ajíes amarillos', 'sin semillas ni venas'],
                        [1, '', 'cebolla roja grande', 'en juliana muy fina'],
                        [0.5, 'taza', 'cilantro fresco', 'solo las hojas'],
                        [1, 'diente', 'ajo', null],
                        [0.5, 'cdta', 'jengibre fresco', 'rallado'],
                        [1, '', 'sal y pimienta blanca', 'al gusto'],
                    ]],
                    ['group' => 'Guarnición Tradicional', 'items' => [
                        [2, '', 'mazorcas de choclo', 'cocidas y desgranadas'],
                        [4, '', 'camotes', 'cocidos y en rodajas'],
                        [8, '', 'hojas de lechuga', 'para presentar'],
                        [1, '', 'ají amarillo extra', 'para decorar'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar la cebolla', 'Corta la cebolla en juliana muy fina. Lava con agua fría por 2 minutos para suavizar el sabor. Escurre bien y reserva.', 5],
                    [2, 'Licuar leche de tigre', 'Licúa el jugo de 6 limones con el ají amarillo (sin semillas), el ajo, jengibre, un trozo pequeño de pescado (50g), sal y pimienta. Cuela y reserva este concentrado.', 5],
                    [3, 'Marinar el pescado', 'Coloca los cubos de corvina en un tazón con sal. Agrega el jugo del resto de limones y la leche de tigre concentrada. Mezcla y deja marinar exactamente 8-12 minutos (no más para que no sobre-cure).', 12],
                    [4, 'Incorporar y servir', 'Agrega la cebolla en juliana y el cilantro. Mezcla suavemente y sirve de inmediato en platos fríos. Acompaña con choclo, camote y lechuga.', 3],
                ],
                'faqs' => [
                    ['¿Es seguro comer el ceviche si el pescado no se cocina con calor?', 'El ácido del limón desnaturaliza las proteínas del pescado (proceso similar a la cocción), pero no elimina todos los patógenos. Por eso es FUNDAMENTAL usar pescado de altísima frescura de una fuente de confianza.'],
                    ['¿Puedo usar pescado congelado?', 'Sí, de hecho congelarlo a -20°C por 24 horas antes elimina posibles parásitos. Descongela en refrigerador y usa de inmediato una vez descongelado.'],
                    ['¿Qué puedo tomar la leche de tigre?', 'La leche de tigre que sobra en el plato se toma como shot. Es considerada afrodisiaca y digestiva. ¡Es la mejor parte del ceviche para muchos!'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 8. ARROZ CON LECHE
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Arroz con Leche Cremoso',
                    'subtitle'         => 'El postre de la abuela, cremoso y perfumado con canela',
                    'description'      => 'El arroz con leche es un postre universal con versiones en toda Latinoamérica. Este es el estilo cremoso latinoamericano: cocido lentamente con leche entera, canela, limón y leche condensada. Irresistible caliente o frío.',
                    'story'            => 'Pocas cosas me transportan tan directamente a la infancia como el aroma del arroz con leche cocinándose. El sonido burbujeante de la leche, el olor a canela que llena la cocina, y la espera impaciente frente al fogón son recuerdos imborrables. Este postre lleva décadas en mi familia, y cada vez que lo preparo siento que el tiempo retrocede.',
                    'tips_secrets'     => "• Lava el arroz hasta que el agua salga transparente para eliminar el exceso de almidón.\n• Cocina a fuego muy bajo y revuelve constantemente para evitar que se pegue.\n• La leche condensada se agrega al final para evitar que se queme.\n• Si queda muy espeso, agrega un poco de leche caliente.\n• La ralladura de limón es opcional pero aporta un toque de frescura irresistible.",
                    'origin_country'   => 'Ecuador',
                    'origin_region'    => 'Latinoamérica',
                    'prep_time_minutes'=> 5,
                    'cook_time_minutes'=> 40,
                    'servings'         => 6,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'easy',
                    'seo_title'        => 'Arroz con Leche Cremoso Latinoamericano | Receta de la Abuela',
                    'seo_description'  => 'Receta de arroz con leche cremoso estilo latinoamericano con canela, limón y leche condensada. El postre más nostálgico y fácil de preparar. ¡Solo 5 ingredientes!',
                    'schema_rating_value' => 4.7,
                    'schema_rating_count' => 445,
                    'view_count'       => 9870,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(8),
                ],
                'category' => 'Postres',
                'tags'     => ['Vegetariano', 'Familiar', 'Sin Gluten'],
                'ingredients' => [
                    ['group' => 'Arroz con Leche', 'items' => [
                        [1, 'taza', 'arroz blanco de grano corto', 'bien lavado'],
                        [1, 'litro', 'leche entera', null],
                        [1, 'taza', 'agua', null],
                        [2, 'ramas', 'canela en rama', null],
                        [1, '', 'cáscara de limón', 'en tiritas, sin parte blanca'],
                        [4, 'clavos de olor', 'enteros', null],
                        [0.5, 'lata', 'leche condensada', 'unos 200g'],
                        [1, 'cdta', 'extracto de vainilla', null],
                        [1, 'pizca', 'sal', null],
                    ]],
                    ['group' => 'Para Decorar', 'items' => [
                        [1, 'cdta', 'canela en polvo', null],
                        [1, '', 'ralladura de limón', 'opcional'],
                    ]],
                ],
                'steps' => [
                    [1, 'Cocinar el arroz', 'Hierve el agua con la canela, clavos y cáscara de limón. Agrega el arroz lavado y una pizca de sal. Cocina a fuego medio hasta que el agua se absorba casi completamente (8 min).', 10],
                    [2, 'Agregar la leche', 'Incorpora la leche caliente (o a temperatura ambiente) de una vez. Baja el fuego al mínimo y cocina revolviendo constantemente cada pocos minutos para evitar que se pegue.', 25],
                    [3, 'Agregar dulce', 'Cuando el arroz esté muy cremoso y la leche haya reducido a la mitad (unos 25 min), agrega la leche condensada y la vainilla. Revuelve bien y cocina 5 minutos más.', 5],
                    [4, 'Ajustar y servir', 'Retira la canela, clavos y cáscara de limón. Verifica la cremosidad y dulzor. Sirve caliente o refrigera. Al servir espolvorea canela en polvo encima.', 5],
                ],
                'faqs' => [
                    ['¿Por qué queda aguado mi arroz con leche?', 'Necesita más tiempo de cocción. Continúa cocinando a fuego bajo revolviendo. La consistencia correcta se logra cuando al dejar caer una cuchara en la superficie, el surco tarda en cerrarse.'],
                    ['¿Se puede hacer sin lactosa?', 'Sí, usa leche sin lactosa o leche de coco (da un sabor tropical delicioso). La leche condensada sin lactosa también existe en supermercados.'],
                    ['¿Cuánto dura en refrigerador?', 'Hasta 5 días bien tapado. Al recalentar agrega un chorrito de leche y revuelve, ya que se espesa al enfriar.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 9. GAZPACHO ANDALUZ
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Gazpacho Andaluz Auténtico',
                    'subtitle'         => 'La sopa fría española más refrescante del verano',
                    'description'      => 'El gazpacho auténtico es una sopa fría de tomate, pimiento, pepino, ajo, aceite de oliva virgen extra y vinagre de Jerez. Sin cocción, sin calor: la técnica perfecta para conservar todos los nutrientes y vitaminas.',
                    'story'            => 'El gazpacho nació como comida de campesinos en Andalucía: pan duro, tomates, aceite de oliva y lo que hubiera. Hoy es bandera de la cocina española moderna. Lo probé por primera vez en Sevilla un verano sofocante de julio, servido helado con una guarnición de verduras picadas, y fue una revelación. La clave, me explicó el cocinero, es el aceite de oliva andaluz: sin él, es otra cosa.',
                    'tips_secrets'     => "• Los tomates deben estar completamente maduros y sabrosos: es el 70% del plato.\n• El aceite de oliva virgen extra de calidad es fundamental para el sabor final.\n• El pan duro (sin corteza) espesa y da cuerpo, no lo omitas.\n• Mínimo 2 horas de frío antes de servir para que los sabores se integren.\n• El vinagre de Jerez es insustituible; el vinagre normal cambia el carácter.",
                    'origin_country'   => 'España',
                    'origin_region'    => 'Andalucía',
                    'prep_time_minutes'=> 15,
                    'cook_time_minutes'=> 0,
                    'rest_time_minutes'=> 120,
                    'servings'         => 6,
                    'servings_unit'    => 'personas',
                    'difficulty'       => 'easy',
                    'seo_title'        => 'Gazpacho Andaluz Auténtico | Receta Original de Sevilla Paso a Paso',
                    'seo_description'  => 'Receta auténtica de gazpacho andaluz con tomates maduros, aceite de oliva virgen y vinagre de Jerez. Sin cocción, fresco y saludable. El verano en un vaso.',
                    'schema_rating_value' => 4.8,
                    'schema_rating_count' => 267,
                    'view_count'       => 7340,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(6),
                ],
                'category' => 'Recetas Mediterráneas',
                'tags'     => ['Vegetariano', 'Sin Gluten', 'Rápido', 'Sin Lactosa'],
                'ingredients' => [
                    ['group' => 'Gazpacho', 'items' => [
                        [1, 'kg', 'tomates maduros', 'tipo pera o rama, bien maduros'],
                        [1, '', 'pimiento rojo', 'sin semillas'],
                        [1, '', 'pepino mediano', 'pelado y sin semillas'],
                        [2, 'dientes', 'ajo', null],
                        [100, 'g', 'pan blanco duro', 'sin corteza, remojado en agua'],
                        [100, 'ml', 'aceite de oliva virgen extra', 'de calidad'],
                        [2, 'cdas', 'vinagre de Jerez', 'o vinagre de vino blanco'],
                        [1, 'cdta', 'sal', 'al gusto'],
                        [200, 'ml', 'agua fría', 'o más al gusto'],
                    ]],
                    ['group' => 'Guarnición (Tropezones)', 'items' => [
                        [1, '', 'tomate pequeño', 'en cubos muy pequeños'],
                        [0.5, '', 'pimiento verde', 'en cubos muy pequeños'],
                        [0.5, '', 'pepino', 'en cubos muy pequeños'],
                        [0.5, '', 'cebolla tierna', 'en cubos muy pequeños'],
                        [4, 'rebanadas', 'pan', 'en cubos, tostado como crutones'],
                    ]],
                ],
                'steps' => [
                    [1, 'Preparar ingredientes', 'Pela los tomates (opcional: escalda 30 seg y pela), pica grosso modo junto con el pimiento y pepino. Remoja el pan en agua fría 5 minutos y exprime bien.', 10],
                    [2, 'Licuar', 'Coloca todos los ingredientes del gazpacho en la licuadora en este orden: ajo, pan, tomate, pimiento, pepino. Licúa a máxima potencia 2-3 minutos hasta obtener una crema completamente lisa.', 5],
                    [3, 'Emulsionar con aceite', 'Con la licuadora en marcha a velocidad baja, agrega el aceite de oliva en hilo fino. Esto emulsiona el gazpacho y le da cremosidad. Agrega el vinagre, sal y agua para ajustar la textura.', 3],
                    [4, 'Colar y refrigerar', 'Pasa por un colador de malla fina presionando con una cuchara para un gazpacho muy fino y sedoso. Refrigera al menos 2 horas (mejor toda la noche).', 5],
                    [5, 'Servir', 'Sirve bien frío en vasos o platos hondos. Acompaña con los tropezones en boles separados para que cada comensal agregue al gusto.', 2],
                ],
                'faqs' => [
                    ['¿Es necesario colar el gazpacho?', 'No es obligatorio, pero colar da una textura más elegante y sedosa. Sin colar queda más rústico y con más fibra, que también está delicioso.'],
                    ['¿Cuánto tiempo se conserva?', 'En refrigerador bien tapado hasta 4-5 días. Agita o revuelve antes de servir porque los componentes tienden a separarse.'],
                    ['¿Puedo agregar jalapeño para un toque picante?', 'Sí, aunque no es tradicional. Agrega un trozo pequeño al licuar y ajusta al gusto. También queda muy bien con unas gotas de tabasco al servir.'],
                ],
            ],

            // ─────────────────────────────────────────────────────────────────
            // 10. TRES LECHES
            // ─────────────────────────────────────────────────────────────────
            [
                'recipe' => [
                    'title'            => 'Pastel Tres Leches Esponjoso',
                    'subtitle'         => 'El postre más húmedo y delicioso de Latinoamérica',
                    'description'      => 'El pastel tres leches es un bizcocho esponjoso empapado en una mezcla de tres leches (condensada, evaporada y crema), coronado con chantilly. Húmedo, suave, dulce y absolutamente irresistible. El postre favorito de toda Latinoamérica.',
                    'story'            => 'El origen exacto del tres leches es disputado entre México, Nicaragua y Ecuador. Lo que no se disputa es su éxito: ningún postre latinoamericano tiene el poder de reunir a la gente alrededor de una mesa como el tres leches. Aprendí esta versión de una pastelera nicaragüense que insistía en que el secreto está en los huevos a temperatura ambiente y no abrir el horno los primeros 25 minutos.',
                    'tips_secrets'     => "• Los huevos y la leche a temperatura ambiente son claves para un bizcocho esponjoso.\n• Haz los agujeros mientras el bizcocho está caliente para mejor absorción.\n• Refrigera mínimo 4 horas; de un día para otro es aún mejor.\n• El chantilly debe estar muy frío para montar correctamente.\n• Puedes agregar ron o brandy a la mezcla de tres leches para versión adulta.",
                    'origin_country'   => 'Ecuador',
                    'origin_region'    => 'Latinoamérica',
                    'prep_time_minutes'=> 30,
                    'cook_time_minutes'=> 30,
                    'rest_time_minutes'=> 240,
                    'servings'         => 12,
                    'servings_unit'    => 'porciones',
                    'difficulty'       => 'medium',
                    'seo_title'        => 'Pastel Tres Leches Esponjoso | Receta Infalible Paso a Paso',
                    'seo_description'  => 'Receta perfecta de pastel tres leches esponjoso: bizcocho húmedo empapado en leche condensada, evaporada y crema, con chantilly. El postre favorito de Latinoamérica.',
                    'schema_rating_value' => 4.9,
                    'schema_rating_count' => 782,
                    'view_count'       => 18900,
                    'is_published'     => true,
                    'published_at'     => Carbon::now()->subDays(3),
                ],
                'category' => 'Postres',
                'tags'     => ['Festivo', 'Familiar', 'Para Compartir'],
                'ingredients' => [
                    ['group' => 'Bizcocho', 'items' => [
                        [5, '', 'huevos grandes', 'a temperatura ambiente, separados'],
                        [200, 'g', 'azúcar', null],
                        [200, 'g', 'harina de trigo', 'tamizada'],
                        [1, 'cdta', 'polvo para hornear', null],
                        [1, 'cdta', 'extracto de vainilla', null],
                        [0.5, 'taza', 'leche entera', 'tibia'],
                        [1, 'pizca', 'sal', null],
                    ]],
                    ['group' => 'Las Tres Leches', 'items' => [
                        [1, 'lata', 'leche condensada (397g)', null],
                        [1, 'lata', 'leche evaporada (370g)', null],
                        [1, 'taza', 'crema de leche', '35% materia grasa'],
                        [2, 'cdas', 'ron blanco', 'opcional'],
                    ]],
                    ['group' => 'Chantilly', 'items' => [
                        [500, 'ml', 'crema para batir', 'muy fría'],
                        [4, 'cdas', 'azúcar glass', 'tamizada'],
                        [1, 'cdta', 'extracto de vainilla', null],
                    ]],
                    ['group' => 'Decoración', 'items' => [
                        [1, 'cdta', 'canela en polvo', null],
                        [8, '', 'fresas frescas', 'para decorar'],
                    ]],
                ],
                'steps' => [
                    [1, 'Batir yemas', 'Precalienta el horno a 175°C. Bate las yemas con el azúcar a velocidad alta hasta que estén pálidas y esponjosas (5-7 min). Agrega la vainilla y la leche tibia. Incorpora la harina tamizada con el polvo para hornear en 2-3 adiciones.', 15],
                    [2, 'Montar claras', 'En un tazón limpio y seco, bate las claras con una pizca de sal a punto de nieve firme (picos duros). Incorpora a la mezcla de yemas en 3 partes con movimientos envolventes, sin sobrebatir.', 10],
                    [3, 'Hornear', 'Vierte en un molde rectangular 23x33 cm engrasado. Hornea 25-30 minutos, NO abras el horno los primeros 25 minutos. Está listo cuando al insertar un palillo, sale limpio.', 30],
                    [4, 'Empapar con tres leches', 'Mezcla las tres leches con el ron. Mientras el bizcocho aún esté caliente, hazle agujeros con un palillo o tenedor en toda la superficie. Vierte lentamente la mezcla de tres leches, dejando que se absorba. Refrigera mínimo 4 horas.', 10],
                    [5, 'Preparar chantilly y decorar', 'Bate la crema fría con el azúcar glass y vainilla hasta picos firmes. Extiende sobre el pastel frío. Decora con fresas y espolvorea canela. Refrigera hasta servir.', 15],
                ],
                'faqs' => [
                    ['¿Por qué mi bizcocho quedó aplastado?', 'Las razones más comunes son: claras sobrebatidas o mezcladas con fuerza, abrir el horno antes de tiempo, o horno a temperatura incorrecta. Usa un termómetro de horno para verificar la temperatura real.'],
                    ['¿Cuánto tiempo dura el tres leches?', 'En refrigerador bien cubierto dura hasta 5 días. De hecho, mejora con los días porque las leches se siguen absorbiendo y los sabores se integran más.'],
                    ['¿Puedo hacerlo sin gluten?', 'Sí, sustituye la harina de trigo por una mezcla de harina de arroz y fécula de maíz (2:1). El resultado es muy similar en sabor y textura.'],
                ],
            ],

        ]; // end $recipes array

        foreach ($recipes as $data) {
            // Find category
            $categoryName = $data['category'];
            $category = Category::where('name', $categoryName)->first();

            // Create recipe
            $recipe = Recipe::create(array_merge($data['recipe'], [
                'user_id' => $admin->id,
            ]));

            // Attach category
            if ($category) {
                $recipe->categories()->attach($category->id, ['is_primary' => true]);
            }

            // Attach tags
            foreach ($data['tags'] as $tagName) {
                if (isset($tags[$tagName])) {
                    $recipe->tags()->attach($tags[$tagName]->id);
                }
            }

            // Create ingredients
            $position = 1;
            foreach ($data['ingredients'] as $group) {
                foreach ($group['items'] as $item) {
                    RecipeIngredient::create([
                        'recipe_id'        => $recipe->id,
                        'order_position'   => $position++,
                        'amount'           => $item[0],
                        'unit'             => $item[1],
                        'ingredient_name'  => $item[2],
                        'ingredient_group' => $group['group'],
                        'notes'            => $item[3] ?? null,
                    ]);
                }
            }

            // Create steps
            foreach ($data['steps'] as $step) {
                RecipeStep::create([
                    'recipe_id'        => $recipe->id,
                    'step_number'      => $step[0],
                    'title'            => $step[1],
                    'description'      => $step[2],
                    'duration_minutes' => $step[3] ?? null,
                ]);
            }

            // Create FAQs
            foreach ($data['faqs'] as $i => $faq) {
                RecipeFaq::create([
                    'recipe_id'  => $recipe->id,
                    'question'   => $faq[0],
                    'answer'     => $faq[1],
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
