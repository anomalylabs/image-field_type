<?php

return [
    'folders'      => [
        'name'         => 'Ordner',
        'instructions' => 'Geben Sie an welche Ordner für dieses Feld verfügbar sind. Leer lassen um alle Ordner anzuzeigen.',
        'warning'      => 'Bestehende Ordnerberechtigungen haben Vorrang gegenüber den ausgewählten Ordnern.',
    ],
    'aspect_ratio' => [
        'name'         => 'Seitenverhältnis',
        'instructions' => 'Geben Sie das Seitenverhältnis des Zuschnittsbereichs an wie z.B. <strong>16:9</strong>, <strong>1:9</strong> or <strong>750:160</strong>.',
    ],
    'min_height'   => [
        'name'         => 'Minimale Höhe',
        'instructions' => 'Geben Sie die minimale Höhe des Zuschnittsbereichs an.',
    ],
    'mode'         => [
        'name'         => 'Eingabe Modus',
        'instructions' => 'Wie sollten Benutzer die Bilder eingeben.',
        'option'       => [
            'default' => 'Bilder hochladen und/oder auswählen.',
            'select'  => 'Nur Bilder auswählen.',
            'upload'  => 'Nur Bilder hochladen.',
        ],
    ],
];
