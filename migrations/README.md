Vous pouvez placer ici vos fichiers SQL permettant d'initialiser ou de modifier la base de données.

## Migrations disponibles

### 0_init.sql
Initialisation complète de la base de données avec toutes les tables.

### 1_add_idlecon.sql
Ajoute un identifiant unique `idlecon` à la table `conduire` pour faciliter l'identification des leçons.

**Pour appliquer cette migration :**
```bash
mysql -u votre_utilisateur -p votre_base_de_donnees < migrations/1_add_idlecon.sql
```
