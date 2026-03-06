<?php
include_once(__DIR__ . '/view/header-login.php');
?>

<main class="auth-container">

    <section class="auth-box login-section">
        <form action="/login/handleLogin" method="POST">
            <h2>Se connecter</h2>

            <div class="field">
                <label for="login_email">Adresse E-mail <span class="required">*</span></label>
                <input type="email" id="login_email" name="email" required placeholder="Ex: nomprenom@xxx.com" />
            </div>

            <div class="field">
                <label for="login_password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="login_password" name="password" required />
            </div>

            <button type="submit" class="btn-submit">Continuer</button>
        </form>
    </section>

    <div class="vertical-separator"></div>

    <section class="auth-box register-section" id="register-section">
        <form action="/login/handleRegister" method="POST">
            <h2>Créez votre compte</h2>

            <div class="field">
                <label for="register_email">Adresse E-mail <span class="required">*</span></label>
                <input type="email" id="register_email" name="email" required />
            </div>

            <div class="field">
                <label for="register_password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="register_password" name="mot_de_passe" required />
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" required />
                </div>
                <div class="field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" required />
                </div>
            </div>

            <div class="field">
                <label for="age">Âge <span class="required">*</span></label>
                <select id="age" name="age" required>
                    <option value="">Sélectionnez votre âge</option>
                    <?php for ($i = 18; $i <= 99; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> ans</option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="accept_conditions" name="accept_conditions" required>
                <label for="accept_conditions">
                    J'accepte les <a href="/condition">Conditions générales d’utilisation<span class="required">*</span></a>.
                </label>
            </div>

            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>
    </section>

</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>