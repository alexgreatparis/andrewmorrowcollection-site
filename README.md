# Andrew Morrow Collection — Webhook de commande

## Configuration du webhook

Le formulaire de commande n'envoie plus vers `process_order.php` (non supporté par GitHub Pages). Il poste maintenant les champs du formulaire vers une URL de webhook configurable via une meta HTML.

Meta à modifier dans `index.html` (section `<head>`):

```html
<meta name="am-webhook" content="https://<votre-webhook>" />
```

Exemple (Pipedream stable):

```html
<meta name="am-webhook" content="https://eo2vtt49hsesicb.m.pipedream.net" />
```

## Notes techniques

- L'envoi utilise `fetch` avec `mode: 'no-cors'` et `FormData` pour éviter tout problème CORS sur des endpoints génériques.
- À la réception, les champs sont disponibles côté webhook sous forme de paramètres `multipart/form-data`:
  - `nom`, `prenom`, `adresse`, `code_postal`, `ville`, `email`, `telephone`, `quantite`, `order_form`.
- La notification de succès/erreur et la redirection sont gérées côté client (`?order=success` ou `?order=error`).

## Tests rapides

1. Mettre à jour la meta `am-webhook`.
2. `git add index.html && git commit && git push`.
3. Ouvrir `https://andrewmorrowcollection.com/?cb=test#achat` et envoyer le formulaire.
4. Vérifier la réception côté webhook.

## Sécurité (optionnel)

Si vous migrez vers un endpoint contrôlé (ex. n8n, API custom), vous pouvez :

- Exiger un header de secret côté serveur et activer `cors` ;
- Ou garder `no-cors` et valider un jeton passé en champ masqué.

Dans ce repo, nous conservons `no-cors` pour une compatibilité maximale avec des services externes.
