import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Barangay/**/*.php',
        './resources/views/filament/barangay/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
