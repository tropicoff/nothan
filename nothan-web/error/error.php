<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erreur | NOTHAN</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap");
                
                body {
                    font-family: "Orbitron", sans-serif;
                    overflow: hidden;
                }
                
                .error-message {
                    text-shadow: 0 0 10px #ff0000, 0 0 20px #ff6b6b;
                }
            </style>
        </head>
        <body class="bg-black text-white">
            <div id="vanta-bg"></div>
            
            <div class="min-h-screen flex items-center justify-center px-4">
                <div class="w-full max-w-md p-8 rounded-lg backdrop-blur-sm bg-black/30 text-center">
                    <h1 class="text-4xl font-bold mb-6 error-message">ACCÈS REFUSÉ</h1>
                    <p class="text-red-400 mb-8">Identifiants incorrects</p>
                    <a href="/" class="inline-block px-6 py-3 rounded-lg font-bold bg-gradient-to-r from-purple-900 to-pink-600 hover:opacity-80 transition">
                        Retour à la connexion
                    </a>
                </div>
            </div>

            <script>
                VANTA.NET({
                    el: "#vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    scale: 1.00,
                    scaleMobile: 1.00,
                    color: 0xff0000,
                    backgroundColor: 0x0,
                    points: 12.00,
                    maxDistance: 22.00,
                    spacing: 18.00
                });
            </script>
        </body>
        </html>';
        exit();
    }
}
?>
