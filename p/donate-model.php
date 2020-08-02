<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/functions.php');
include_start_html("Donate Your 3D Asset");
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/header.php');
?>

<div id="page-wrapper">
    <h1>Donate Your 3D Asset</h1>
    <p>Have a top-notch 3D model you'd like to publish on 3D Model Haven?
    <a href="https://forms.gle/jvaKUR6KPcVfhLiU6" target="_blank">
        <span class='button' style='margin-left:1em; font-size:1em'>Submit it here</span>
    </a>
    </p>
    <?php echo md_to_html("

## Important

Your asset must be **100% your own original work.** By sending us your asset, you will be releasing it under the [CC0 license](/p/license.php), forfeiting your copyright.

Once we upload it to 3D Model Haven, anyone will be able to do anything they want with your asset, which is awesome and really generous of you, but it's important you understand that and know that it **cannot be revoked**. Once you declare something as CC0, it's in the public domain forever and cannot ever be made private again.

__**You cannot include any copyrighted materials in your asset**__ such as textures that you didn't make yourself, unless you have explicit permission to redistribute derivative work as CC0, or the textures are CC0 themselves.

If your asset includes any real-world logo, trademark, or copyrighted design, we cannot accept it.

Once your asset is approved, and before we publish it, you'll be signing a legal agreement to confirm all of this.

## Requirements

We value quality over quantity, and unfortunately that means we have to be very strict about reviewing your asset in order to maintain a high standard of quality on 3D Model Haven.

If you're not sure your work is good enough for us, you can ask on our Discord: https://discord.gg/Dms7Mrs

Your model should be:

* Photorealistic, or stylized in a way that many people would find appealing and useful.
* Fully UV unwrapped and textured with standard PBR maps.
* Minimum 4k resolution textures.
* In real-world scale.
* Uses only custom original textures, or textures that are CC0/public domain.
* In either \".blend\" format, or a format we can import into Blender (fbx, gltf, etc.).

## Is there payment for this?

No, sorry. We have a very limitted budget from Patreon donations, which we spend to fill in the gaps between donated assets. However, when we hire artists to create new content, donors like you are the first people we go to.

This may change in future, but for now it's simply a selfless donation to the community.

    "); ?>

    <div class='center'>
        <a href="https://forms.gle/jvaKUR6KPcVfhLiU6" target="_blank">
            <div class='button'>Submit your model</div>
        </a>
    </div>

</div>

<?php
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/footer.php');
include ($_SERVER['DOCUMENT_ROOT'].'/php/html/end_html.php');
?>
