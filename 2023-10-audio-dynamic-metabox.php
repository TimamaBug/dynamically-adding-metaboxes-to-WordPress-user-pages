
<?php
function ajouter_metabox_audio() {
    add_meta_box(
        'metabox_audio',
        'Télécharger des fichiers audio',
        'afficher_metabox_audio',
        'page', // Remplacez 'page' par le type de contenu où vous voulez afficher le métabox
        'normal',
        'default' // ou core
    );
}
add_action('add_meta_boxes', 'ajouter_metabox_audio');

// Fonction pour afficher le métabox
function afficher_metabox_audio($post) {
    wp_nonce_field(basename(__FILE__), 'metabox_audio_nonce');

    // Récupérer les fichiers audio attachés à la page
    $audio_files = get_post_meta($post->ID, 'audio_files', true);
    $audio_titles = get_post_meta($post->ID, 'audio_titles', true);
    $audio_styles = get_post_meta($post->ID, 'audio_styles', true);

    if (!$audio_files) {
        $audio_files = array();
    }

    ?>
    <div id="audio-container">
        <?php foreach ($audio_files as $index => $audio) : ?>
            <ul class="audio-row">
                <li style="display:inline;" ><label for="audio-title-<?php echo $index; ?>">Titre :</label></li>
                <li style="display:inline;" ><input type="text" name="audio_titles[]" id="audio-title-<?php echo $index; ?>" value="<?php echo esc_attr($audio_titles[$index]); ?>"></li>
                <ul class="audio-style" style="width: 150px; display: inline;">
                    <li style="display:inline;" ><label>Style 1</label> </li>
                    <li style="display:inline;" ><input type="radio" name="audio_styles[<?php echo $index; ?>]" value="style1" <?php echo ($audio_styles[$index] === 'style1') ? 'checked' : ''; ?>></li>
                    <li style="display:inline;" ><label>Style 2</label> </li>
                    <li style="display:inline;" ><input type="radio" name="audio_styles[<?php echo $index; ?>]" value="style2" <?php echo ($audio_styles[$index] === 'style2') ? 'checked' : ''; ?>></li>
                </ul>
                <li style="display:inline;"> <input name="audio_files[]" value="<?php echo esc_url($audio); ?>"></li>
                <li style="display:inline;"> <button class="remove-audio-button" type="button">Supprimer</button> </li>
            </ul>
        <?php endforeach; ?>
    </div>
    <button id="add-audio-button" type="button">Ajouter un fichier audio</button>

    <script>
        jQuery(document).ready(function($) {
            // Gérer l'ajout de fichiers audio
            $('#add-audio-button').click(function() {
                wp.media.editor.send.attachment = function(props, attachment) {
                    $('#audio-container').append(`
                        <ul class="audio-row">
                            <li style="display:inline;"><label for="audio-title-<?php echo count($audio_files); ?>">Titre :</label></li>
                            <li style="display:inline;"><input type="text" name="audio_titles[]" id="audio-title-<?php echo count($audio_files); ?>" value=""></li>
                            <ul class="audio-style" style="width: 150px; display: inline;"></li>
                                <li style="display:inline;"><label>Style 1</label></li>
                                <li style="display:inline;"> <input type="radio" name="audio_styles[]" value="style1" checked></li>
                                <li style="display:inline;"> <label>Style 2</label></li>
                                <li style="display:inline;"> <input type="radio" name="audio_styles[]" value="style2"></li>
                            </ul>
                            <li style="display:inline;"><input name="audio_files[]" value="${attachment.url}"></li>
                            <li style="display:inline;"> <button class="remove-audio-button" type="button">Supprimer</button></li>
                        </ul>
                    `);
                };
                wp.media.editor.open();
                return false;
            });

            // Gérer la suppression de fichiers audio
            $('#audio-container').on('click', '.remove-audio-button', function() {
                $(this).closest('.audio-row').remove(); // Supprimer la ligne parente
            });
        });
    </script>
    <?php
}

// Fonction pour sauvegarder les données du métabox
function sauvegarder_metabox_audio($post_id) {
    if (!isset($_POST['metabox_audio_nonce']) || !wp_verify_nonce($_POST['metabox_audio_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
        // Sauvegarder les fichiers audio
        if (isset($_POST['audio_files'])) {
            update_post_meta($post_id, 'audio_files', $_POST['audio_files']);
        }

        // Sauvegarder les titres des fichiers audio
        if (isset($_POST['audio_titles'])) {
             update_post_meta($post_id, 'audio_titles', $_POST['audio_titles']);
        }

        // Sauvegarder les styles
        if (isset($_POST['audio_styles'])) {
            update_post_meta($post_id, 'audio_styles', $_POST['audio_styles']);
        }
        
    return $post_id;
}

add_action('save_post', 'sauvegarder_metabox_audio');
