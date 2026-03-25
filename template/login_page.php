<?php
include_once(__DIR__ . '/view/header-login.php');
?>

<main class="auth-container">
    <section class="auth-box login-section">
        <form action="/login/handleLogin" method="POST">
            
    <!-- Formulaire de connexion -->

            <h2>Se connecter</h2>

            <div class="field">
                <label for="login_email">Adresse E-mail <span class="required">*</span></label>
                <input type="email" id="login_email" name="email" required placeholder="nom@exemple.com" />
            </div>

            <div class="field">
                <label for="login_password">Mot de passe <span class="required">*</span></label>
                <div class="input-wrapper">
                    <input type="password" id="login_password" name="password" required placeholder="••••••••" />
                    <button type="button" class="toggle-password-btn" onclick="togglePassword('login_password', 'eyeIconLogin')">
                        <svg id="eyeIconLogin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Continuer</button>
        </form>
    </section>

    <div class="vertical-separator"></div>

    <section class="auth-box register-section" id="register-section">
        <form action="/login/handleRegister" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['flash_error']);
                unset($_SESSION['flash_error']);
                ?>
                </div>
            <?php endif; ?>

    <!-- Formulaire d'inscription -->
            
            <h2>Créez votre compte</h2>
            
            <div class="field">
                <label for="register_email">Adresse E-mail <span class="required">*</span></label>
                <input type="email" id="register_email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label for="register_password">Mot de passe <span class="required">*</span></label>
                <div class="input-wrapper">
                    <input type="password" id="register_password" name="password" required 
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w]).{12,}" />
                    <button type="button" class="toggle-password-btn" onclick="togglePassword('register_password', 'eyeIconRegister')">
                        <svg id="eyeIconRegister" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <small class="password-hint">
                    12 caractères min. (Majuscule, chiffre et symbole requis)
                </small>
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
                    <option value="">--</option>
                    <?php for ($i = 18; $i <= 99; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?> ans</option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="checkbox-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="accept_conditions" name="accept_conditions" required>
                    <label for="accept_conditions">
                        J'accepte les <a href="/condition" class="link-styled">Conditions générales d’utilisation</a><span class="required">*</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit">Créer mon compte</button>
        </form>
    </section>
</main>

<?php
include_once(__DIR__ . '/view/footer.php');
?>