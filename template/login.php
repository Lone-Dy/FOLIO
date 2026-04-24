<?php
$flashMessages = $_SESSION['flash_messages'] ?? [];
unset($_SESSION['flash_messages']);
?>

<div class="auth-container">

    <!-- Formulaire de connexion -->

    <section class="auth-box login-section">
        <form action="/login/handleLogin" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <?php if (!empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert <?= ($type === 'error') ? 'error-message' : 'success-message'; ?>">
                            <?= htmlspecialchars($message); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <h2>Se connecter</h2>

            <div class="field">
                <label for="login_email">E-mail <span class="required">*</span></label>
                <input type="email" id="login_email" name="email" required placeholder="nom@exemple.com" 
                value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>"/>
            </div>

            <div class="field">
                <label for="login_password">Mot de passe <span class="required">*</span></label>
                <div class="input-wrapper">
                    <input type="password" id="login_password" name="password" required placeholder="••••••••••••"/>
                </div>
            </div>

            <button type="submit" class="btn-submit">Continuer</button>
        </form>
    </section>

    <div class="vertical-separator"></div>

    <!-- Formulaire d'inscription -->

    <section class="auth-box register-section" id="register-section">
        <form action="/login/handleRegister" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <?php if (!empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert <?= ($type === 'error') ? 'error-message' : 'success-message'; ?>">
                            <?= htmlspecialchars($message); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <h2>Créez votre compte</h2>

            <div class="form-grid">
                <div class="field">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" required
                        value="<?php echo htmlspecialchars($_SESSION['form_data']['prenom'] ?? ''); ?>" />
                </div>
                <div class="field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" required
                        value="<?php echo htmlspecialchars($_SESSION['form_data']['nom'] ?? ''); ?>" />
                </div>
            </div>

            <div class="field">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" required placeholder="nom@exemple.com"
                    value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" />
            </div>

            <div class="field">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" required placeholder="••••••••••••" />

            </div>

            <div class="field">
                <label for="password_confirmation">Confirmer le mot de passe <span class="required">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••••••" />
            </div>

            <div class="field">
                <label for="age">Âge <span class="required">*</span></label>
                <select id="age" name="age" required>
                    <option value="">--</option>
                    <?php for ($i = 18; $i <= 99; $i++): ?>
                        <option value="<?php echo $i; ?>"
                            <?php echo (isset($_SESSION['form_data']['age']) && $_SESSION['form_data']['age'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?> ans
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="checkbox-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="accept_conditions" name="accept_conditions" required>
                    <label for="accept_conditions">
                        J'accepte les <a href="/condition" class="link-styled">Conditions générales d'utilisation</a><span class="required">*</span>
                    </label>
                </div>
            </div>

            <button type="submit" name="register" class="btn-submit">Créer mon compte</button>
        </form>
    </section>
</div>