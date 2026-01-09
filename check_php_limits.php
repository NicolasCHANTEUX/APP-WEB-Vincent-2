<?php
echo "=== CONFIGURATION PHP UPLOAD ===\n\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . " secondes\n";
echo "max_input_time: " . ini_get('max_input_time') . " secondes\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "\n=== CALCUL POUR 6 IMAGES DE 6 MB ===\n";
echo "Total estimé: 36 MB\n";
echo "Après traitement (3 versions par image): ~108 MB en mémoire\n";
