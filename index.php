<?php

$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function getQueryParam($name, $default = '')
{
    return isset($_GET[$name]) ? htmlspecialchars($_GET[$name], ENT_QUOTES, 'UTF-8') : $default;
}

$authorName = getQueryParam('author');
$authorUrl = getQueryParam('authorurl');

$providerName = getQueryParam('provider');
$providerUrl = getQueryParam('providerurl');

$title = getQueryParam('title');
$description = getQueryParam('description');
$color = getQueryParam('color');
$image = getQueryParam('image');
$imagetype = getQueryParam('imagetype');
$redirect = getQueryParam('redirect');
$passthrough = getQueryParam('passthrough');

$url = getQueryParam('url');

// Generate oEmbed JSON
$oEmbedJson = json_encode([
    "author_name" => $authorName,
    "author_url" => $authorUrl,
    "provider_name" => $providerName,
    "provider_url" => $providerUrl,
]);

if (getQueryParam('format') === 'json') {
    header('Content-Type: application/json');
    echo $oEmbedJson;
    die;
} else {
    header('Content-Type: text/html');
    if (!empty($authorName) || !empty($providerName)) {
        echo "<link rel=\"alternate\" type=\"application/json+oembed\" href=\"" . $actual_link . urlencode($path) . "&format=json\" />";
    }
}

$path = isset($_GET['path']) ? $_GET['path'] : '';
$safePath = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');

echo "<meta name='og:title' content='{$safePath}'>";

if (!empty($redirect)) {
    // if (isset($passthrough)) {
    // header('Location: '.$redirect);
    // } else {
    echo "<script>location = '{$redirect}'</script>";
    // }
}

if (!empty($url)) {
    echo "<script>location = '{$url}'</script>";
}

if (!empty($color)) {
    echo "<meta name='theme-color' content='#{$color}' data-react-helmet='true'>";
}

if (!empty($image)) {
    if ($imagetype == 'thumbnail') {
        echo "<meta name='og:image' content='{$image}'>";
    } else {
        echo "<meta name='og:image' content='{$image}'><meta name='twitter:card' content='summary_large_image'>";
    }
}

if (!empty($title)) {
    echo "<meta name='og:title' content='{$title}'>";
}

if (!empty($description)) {
    echo "<meta name='og:description' content='{$description}'>";
}

?><?php if (empty($redirect) && empty($url) && $_GET['format'] !== 'json') : ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jonas' Discord embed tool</title>
    <link rel="stylesheet" href="assets/output.css">

    <!-- fonts -->
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://fonts.cdnfonts.com/css/gg-sans-2" rel="stylesheet">

    <!-- cdn -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jns.gg/ntoast.js"></script>
    <link src="https://thednp.github.io/color-picker/css/color-picker.css">

    <?php if (empty($_GET)) : ?>
        <!-- seo meta tags -->
        <meta name="description" content="Create custom Discord embeds with Jonas' Embed Generator. Easily make Discord embeds with custom titles, descriptions, colors, and images.">
        <meta name="keywords" content="Discord, Embed Generator, Custom Discord Embeds, Discord Embed Link, Jonas Discord Tools, Embed Builder">
        <meta name="author" content="jns.gg">

        <!-- open graph -->
        <meta property="og:title" content="Jonas' Discord Embed Generator">
        <meta property="og:description" content="Create custom Discord embeds with Jonas' Embed Generator. Easily make Discord embeds with custom titles, descriptions, colors, and images.">
        <meta property="og:url" content="https://embed.jns.gg">
        <meta property="og:type" content="website">

        <!-- x.com -->
        <meta name="twitter:title" content="Jonas' Discord Embed Generator">
        <meta name="twitter:description" content="Create custom Discord embeds with Jonas' Embed Generator. Easily make Discord embeds with custom titles, descriptions, colors, and images.">
    <?php endif; ?>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: "Inter", system-ui;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        .gg-bold {
            font-family: 'gg sans', sans-serif, system-ui;
        }

        .gg-normal {
            font-family: 'gg sans Normal', sans-serif, system-ui;
        }

        .gg-medium {
            font-family: 'gg sans Medium', sans-serif, system-ui;
        }

        .gg-semibold {
            font-family: 'gg sans SemiBold', sans-serif, system-ui;
        }
    </style>
</head>


<body class="overflow-hidden bg-zinc-100 dark:bg-black dark:text-white h-svh">
    <main class="h-svh grid-cols-1 max-w-5xl grid-rows-[fit-content,1fr] gap-4 md:gap-0 mx-auto grid md:grid-cols-2">
        <section class="p-4 overflow-scroll md:order-2">
            <p class="opacity-40">preview</p>
            <div class="text-white bg-[#313338] rounded embedInput focus:ring dark:bg-zinc-800">
                <div class="p-4">
                    <div class="grid grid-cols-[max-content,1fr] gap-2">
                        <img src="https://cdn.discordapp.com/embed/avatars/0.png" alt="default discord avatar" class="grid grid-cols-1 rounded-full size-10">
                        <div class="relative grid grid-cols-1">
                            <span class="leading-5 text-red-500 truncate gg-medium">username</span>
                            <div x-data="{ open: false }" class="truncate leading-5 text-[#02A7FB]">
                                <span @mouseover="open = true" @mouseover.outside="open = false" @click="open = false; navigator.clipboard.writeText(generatedLink.textContent); nToast('Copied link to clipboard')" id="previewLink" class="w-full cursor-pointer gg-normal hover:underline"></span>
                                <div x-cloak x-show="open" x-transition class="absolute z-40 px-3 py-2 text-black bg-white rounded shadow dark:bg-zinc-800 dark:text-white">
                                    click to copy
                                </div>
                            </div>
                            <div class="relative mt-1 rounded-lg bg-[#2B2D30] overflow-hidden">
                                <div id="previewColor" class="absolute top-0 left-0 w-1 h-full"></div>
                                <div class="grid grid-cols-1 gap-2 py-4 pl-5 pr-4 has-[#previewThumbnail:not(.hidden)]:grid-cols-[1fr,max-content]">
                                    <div class="grid grid-cols-1 gap-2">
                                        <div id="previewProvider" class="hidden w-full text-xs break-words opacity-80 gg-normal">title</div>
                                        <div id="previewAuthor" class="hidden w-full text-sm break-words gg-bold">title</div>
                                        <h3 id="previewTitle" class="text-[#02A7FB] break-words hidden gg-semibold">title</h3>
                                        <div class="hidden text-sm break-words gg-normal" id="previewDescription"></div>
                                    </div>
                                    <img id="previewImage" class="hidden w-full rounded">
                                    <img src="previewThumbnail" id="previewThumbnail" class="hidden w-auto h-20 rounded" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 has-[#generatedLink:empty]:hidden">
                <p class="opacity-40">result</p>
                <div class="flex items-center gap-2">
                    <span class="block px-3 py-2 overflow-x-scroll border rounded whitespace-nowrap grow dark:border-zinc-800 border-zinc-300" id="generatedLink">https://embed.jns.gg/?</span>
                    <button onclick="navigator.clipboard.writeText(generatedLink.textContent); nToast('Copied link to clipboard')" class="px-3 py-2 font-medium text-white bg-blue-500 rounded outline-none hover:opacity-90 focus-visible:ring active:tranzinc-y-px">copy</button>
                </div>
            </div>
        </section>
        <section class="overflow-scroll bg-white md:m-4 md:rounded-xl dark:bg-zinc-900">
            <div class="grid grid-cols-1 gap-4 p-4 overflow-y-scroll">
                <hgroup>
                    <h1>discord embed link generator</h1>
                    <p><span class="opacity-40">made by </span><a class="text-blue-500 hover:underline" href="https://jns.gg">Jonas</a></p>
                    <hr class="my-4 dark:border-zinc-700 border-zinc-200">
                </hgroup>
                <label class="block">
                    <p class="opacity-40">title</p>
                    <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="text" id="title">
                </label>
                <label class="block">
                    <p class="opacity-40">description</p>
                    <textarea class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="text" id="description"></textarea>
                </label>
                <label class="flex items-center gap-2">
                    <p class="opacity-40">color</p>
                    <input type="color" id="color" class="embedInput">
                </label>

                <label class="block ">
                    <p class="opacity-40">image url</p>
                    <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="url" id="image" placeholder="https://">
                    <label class="flex items-center gap-2 py-2 cursor-pointer">
                        <input class="embedInput" type="checkbox" switch name="display as thumbnail" id="displayAsThumbnail">
                        <span class="opacity-40">display as thumbnail</span>
                    </label>
                </label>
            </div>

            <details>
                <summary class="px-3 py-2 m-4 font-medium border rounded outline-none cursor-pointer dark:border-zinc-800 border-zinc-300 text-zinc-500 hover:opacity-90 focus-visible:ring active:tranzinc-y-px">
                    advanced settings
                </summary>
                <div class="grid grid-cols-1 gap-4 p-4">
                    <label class="block">
                        <p class="opacity-40">provider name</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="text" id="provider">
                    </label>
                    <label class="block">
                        <p class="opacity-40">provider url</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="url" id="providerUrl" placeholder="https://">
                    </label>
                    <label class="block">
                        <p class="opacity-40">author name</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="text" id="author">
                    </label>
                    <label class="block">
                        <p class="opacity-40">author url</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="url" id="authorUrl" placeholder="https://">
                    </label>
                    <label class="block" l>
                        <p class="opacity-40">redirect url</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="url" id="redirect" placeholder="https://">
                    </label>
                    <label class="block">
                        <p class="opacity-40">link mask</p>
                        <input class="w-full px-3 py-2 border rounded outline-none bg-zinc-200/25 dark:border-zinc-700 border-zinc-200 embedInput focus:ring dark:bg-zinc-800" type="text" id="mask">
                    </label>
                </div>
            </details>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/@thednp/color-picker/dist/js/color-picker.min.js"></script>
    <script>
        var myPicker = new ColorPicker('#myPicker');
    </script>
    <script src="assets/main.js"></script>
</body>

</html>
<?php endif; ?>