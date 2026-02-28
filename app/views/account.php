<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/assets/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
        <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">
        <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    </head>
    <body>
        <div class="login">
            <div class="login__content login__content--wide">
                <div class="login__forms">
                    <div class="login__header">
                        <span class="login__badge"><i class="bx bx-user-circle"></i> Account</span>
                        <h2 class="login__heading">Compte</h2>
                        <?php if ($isOwnAccount): ?>
                            <p class="login__subtitle">Gestion de ton compte, de tes articles, de ton solde et de tes factures.</p>
                        <?php else: ?>
                            <p class="login__subtitle">Consultation du profil public et des articles publies.</p>
                        <?php endif; ?>
                    </div>

                    <div class="account__actions">
                        <a class="account__link" href="<?= htmlspecialchars(app_url('/home'), ENT_QUOTES, 'UTF-8') ?>">Retour accueil</a>
                        <a class="account__link" href="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>">Mon compte</a>
                    </div>

                    <?php if ($errors): ?>
                        <div class="form__message form__message--error">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($successes): ?>
                        <div class="form__message form__message--success">
                            <ul>
                                <?php foreach ($successes as $success): ?>
                                    <li><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($account): ?>
                        <section class="account__section">
                            <h3 class="account__title">Informations du compte</h3>
                            <div class="account__grid">
                                <div class="account__card">
                                    <p><strong>Username:</strong> <?= htmlspecialchars((string)$account['username'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($accountEmailForDisplay, ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Role:</strong> <?= htmlspecialchars((string)$account['role'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p>
                                        <strong>Solde:</strong>
                                        <?php if ($isOwnAccount): ?>
                                            <?= number_format((float)$account['balance'], 2, ',', ' ') ?> EUR
                                        <?php else: ?>
                                            Prive
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="account__section">
                            <h3 class="account__title">Articles publies par ce compte</h3>
                            <?php if (!$publishedArticles): ?>
                                <p class="login__subtitle">Aucun article publie.</p>
                            <?php else: ?>
                                <div class="account__list">
                                    <?php foreach ($publishedArticles as $article): ?>
                                        <article class="account__card">
                                            <p><strong>#<?= (int)$article['id'] ?> - <?= htmlspecialchars((string)$article['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                                            <p><?= htmlspecialchars((string)($article['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                            <p><strong>Prix:</strong> <?= number_format((float)$article['price'], 2, ',', ' ') ?> EUR</p>
                                            <p><strong>Publication:</strong> <?= htmlspecialchars((string)$article['published_at'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </section>

                        <?php if ($isOwnAccount): ?>
                            <section class="account__section">
                                <h3 class="account__title">Articles achetes</h3>
                                <?php if (!$purchasedArticles): ?>
                                    <p class="login__subtitle">Aucun article achete trouve dans les donnees disponibles.</p>
                                <?php else: ?>
                                    <div class="account__list">
                                        <?php foreach ($purchasedArticles as $article): ?>
                                            <article class="account__card">
                                                <p><strong>#<?= (int)$article['id'] ?> - <?= htmlspecialchars((string)$article['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                                                <p><strong>Vendeur:</strong> <?= htmlspecialchars((string)($article['author_username'] ?? 'Inconnu'), ENT_QUOTES, 'UTF-8') ?></p>
                                                <p><strong>Prix:</strong> <?= number_format((float)$article['price'], 2, ',', ' ') ?> EUR</p>
                                                <p><strong>Publication:</strong> <?= htmlspecialchars((string)$article['published_at'], ENT_QUOTES, 'UTF-8') ?></p>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </section>

                            <section class="account__section">
                                <h3 class="account__title">Factures</h3>
                                <?php if (!$invoices): ?>
                                    <p class="login__subtitle">Aucune facture disponible.</p>
                                <?php else: ?>
                                    <div class="account__list">
                                        <?php foreach ($invoices as $invoice): ?>
                                            <article class="account__card">
                                                <p><strong>Facture #<?= (int)$invoice['id'] ?></strong></p>
                                                <p><strong>Date:</strong> <?= htmlspecialchars((string)$invoice['transaction_date'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <p><strong>Montant:</strong> <?= number_format((float)$invoice['amount'], 2, ',', ' ') ?> EUR</p>
                                                <p><strong>Adresse:</strong> <?= htmlspecialchars((string)$invoice['billing_address'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <p><strong>Ville:</strong> <?= htmlspecialchars((string)$invoice['billing_city'], ENT_QUOTES, 'UTF-8') ?></p>
                                                <p><strong>Code postal:</strong> <?= htmlspecialchars((string)$invoice['billing_zip_code'], ENT_QUOTES, 'UTF-8') ?></p>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </section>

                            <section class="account__section">
                                <h3 class="account__title">Modifier les informations</h3>
                                <form method="post" class="account__form" action="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="action" value="update_email">
                                    <div class="login__box">
                                        <i class="bx bx-at login__icon"></i>
                                        <input
                                            type="email"
                                            name="email"
                                            class="login__input"
                                            placeholder="Nouvelle adresse email"
                                            value="<?= htmlspecialchars((string)$account['email'], ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                            autocomplete="email"
                                        >
                                    </div>
                                    <div class="login__box">
                                        <i class="bx bx-lock login__icon"></i>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="login__input"
                                            placeholder="Mot de passe actuel (validation)"
                                            required
                                            autocomplete="current-password"
                                        >
                                    </div>
                                    <button type="submit" class="login__button">Mettre a jour l'email</button>
                                </form>

                                <form method="post" class="account__form" action="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="action" value="update_password">
                                    <div class="login__box">
                                        <i class="bx bx-lock login__icon"></i>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="login__input"
                                            placeholder="Mot de passe actuel"
                                            required
                                            autocomplete="current-password"
                                        >
                                    </div>
                                    <div class="login__box">
                                        <i class="bx bx-lock-alt login__icon"></i>
                                        <input
                                            type="password"
                                            name="new_password"
                                            class="login__input"
                                            placeholder="Nouveau mot de passe"
                                            required
                                            autocomplete="new-password"
                                        >
                                    </div>
                                    <div class="login__box">
                                        <i class="bx bx-check-shield login__icon"></i>
                                        <input
                                            type="password"
                                            name="confirm_password"
                                            class="login__input"
                                            placeholder="Confirmer le nouveau mot de passe"
                                            required
                                            autocomplete="new-password"
                                        >
                                    </div>
                                    <button type="submit" class="login__button">Mettre a jour le mot de passe</button>
                                </form>
                            </section>

                            <section class="account__section">
                                <h3 class="account__title">Ajouter de l'argent au solde</h3>
                                <p class="account__note">
                                    Maximum par ajout: <?= htmlspecialchars($topupMaxPerOperationDisplay, ENT_QUOTES, 'UTF-8') ?> EUR.
                                    Solde maximal autorise: <?= htmlspecialchars($maxBalanceDisplay, ENT_QUOTES, 'UTF-8') ?> EUR.
                                </p>
                                <form method="post" class="account__form" action="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="action" value="add_balance">
                                    <div class="login__box">
                                        <i class="bx bx-wallet login__icon"></i>
                                        <input
                                            type="number"
                                            min="0.01"
                                            max="<?= htmlspecialchars($topupMaxPerOperationInput, ENT_QUOTES, 'UTF-8') ?>"
                                            step="0.01"
                                            name="amount"
                                            class="login__input"
                                            placeholder="Montant a ajouter"
                                            required
                                            autocomplete="off"
                                        >
                                    </div>
                                    <div class="login__box">
                                        <i class="bx bx-lock login__icon"></i>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="login__input"
                                            placeholder="Mot de passe actuel (validation)"
                                            required
                                            autocomplete="current-password"
                                        >
                                    </div>
                                    <button type="submit" class="login__button">Ajouter au solde</button>
                                </form>
                            </section>

                            <section class="account__section account__danger">
                                <h3 class="account__title">Zone sensible</h3>
                                <p class="account__note">
                                    Pour supprimer ton compte, saisis exactement: <strong>SUPPRIMER MON COMPTE</strong>.
                                </p>
                                <form method="post" class="account__form" action="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="action" value="delete_account">
                                    <div class="login__box">
                                        <i class="bx bx-error-circle login__icon"></i>
                                        <input
                                            type="text"
                                            name="confirmation_text"
                                            class="login__input"
                                            placeholder="SUPPRIMER MON COMPTE"
                                            required
                                            autocomplete="off"
                                        >
                                    </div>
                                    <div class="login__box">
                                        <i class="bx bx-lock login__icon"></i>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="login__input"
                                            placeholder="Mot de passe actuel"
                                            required
                                            autocomplete="current-password"
                                        >
                                    </div>
                                    <button type="submit" class="account__danger-button">Supprimer definitivement mon compte</button>
                                </form>
                            </section>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>
