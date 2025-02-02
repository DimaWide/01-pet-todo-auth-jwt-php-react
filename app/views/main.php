<?php

include 'parts/header.php'; // Include header part
?>

<main class="wcl-page-content">
    <div class="data-container wcl-container">
        <?php
        // Render the dynamic content
        echo $content;
        ?>
    </div>
</main>

<?php
include 'parts/footer.php'; // Include footer part
?>