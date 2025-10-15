# ngrok helper scripts

Fichiers créés:
- `start-ngrok.ps1` : démarre ngrok pour exposer votre serveur local (par défaut port 80), lit l'API locale d'ngrok puis écrit l'URL publique dans `ngrok-url.txt` à la racine du dépôt.
- `get-ngrok-url.ps1` : lit et affiche le contenu de `ngrok-url.txt`.

Exemples d'utilisation (PowerShell) :

Démarrer ngrok pour le port 80 :

```powershell
cd C:\wamp64\www\groupe-sup-formation\supformation\tools
.\start-ngrok.ps1 -Port 80
```

Récupérer l'URL publique :

```powershell
.\get-ngrok-url.ps1
```

Remarques :
- Les scripts utilisent le binaire ngrok situé dans `public/ngrok-v3-stable-windows-amd64\ngrok.exe` (fourni dans votre repo). Si ngrok se trouve ailleurs, utilisez le paramètre `-NgrokPath` pour `start-ngrok.ps1`.
- `start-ngrok.ps1` démarre ngrok en arrière-plan et écrit l'URL publique dans `ngrok-url.txt`.
- Assurez-vous de respecter la politique de sécurité de ngrok (token, limites) si vous exposez des services sensibles.
