  <?php
    include_once(__DIR__ . '/view/header.php');
    ?>


  <main>

      <!-- SE CONNECTER -->

      <section class="login-section">
          <form action="login.php" method="POST">
              <h2>Se connecter</h2>
              <p>Vous êtes un nouvel utilisateur ?<a href="#register-section">Créez un compte</a></p>
      </section>
      <div>
          <label for="login_email">Adresse E-mail :
              <span class="required">*</span>
          </label>
          <input type="email" id="login_email" name="email" required aria-required="true"
              placeholder="Ex: nomprenom@xxx.com" />
      </div>
      <button type="submit" class="btn-submit">Continuer</button>
      </form>
      </section>

      <!-- S'INSCRIRE -->

      <hr>
      <section class="register-section">
          <form action="register.php" method="POST">
              <h2>Créez votre compte</h2>
              <p>S’inscrire avec une adresse e-mail</P>
              <p>Vous avez déjà un compte ?<a href="">Connectez-vous</a></p>
              <div>
                  <label for="register_email">Adresse E-mail :
                      <span class="required">*</span>
                  </label>
                  <input type="email" id="register_email" name="email" required aria-required="true"
                      placeholder="Ex: nomprenom@xxx.com" />
                  <label for="password">mot de passe</label>
                  <input id="password" name="password" type="password">
              </div>
              <button type="submit" class="btn-submit">Continuer</button>
          </form>
      </section>

      <!-- IDENTITE -->

      <form>
          <section>
              <h2>Créez votre compte</h2>
              <p>S’inscrire avec une adresse e-mail</P>
              <p>Vous avez déjà un compte ?<a href="">Connectez-vous</a></p>
              <div>
                  <label for="prenom">Prénom :
                      <span class="required">*</span>
                  </label>
                  <input type="text" id="prenom" name="prenom" required aria-required="true" placeholder="Ex: Jean" />
                  <label for="nom">Nom :
                      <span class="required">*</span>
                  </label>
                  <input type="text" id="nom" name="nom" required aria-required="true" placeholder="Ex: Martin" />
              </div>
              <div>
                  <label for="age">Âge :
                      <span class="required">*</span>
                  </label>
                  <select id="age" name="age" required aria-required="true">
                      <option value="">Sélectionnez votre âge</option>
                      <?php
                        for ($i = 18; $i <= 99; $i++) {
                            echo "<option value='$i'>$i ans</option>";
                        }
                        ?>
                  </select>
              </div>
              <div class="checkbox-group">
                  <input type="checkbox" id="accept_conditions" name="accept_conditions" required>
                  <label for="accept_conditions">
                      Je déclare avoir lu et accepter les
                      <a href="conditions-utilisation.php" target="_blank">Conditions d’utilisation</a>
                      et la
                      <a href="politique-confidentialite.php" target="_blank">Politique de confidentialité</a>.
                  </label>
              </div>

              <button type="submit" class="btn-submit">Créer mon compte</button>
      </form>
      </section>
  </main>

  <?php
    include_once(__DIR__ . '/view/footer.php');
    ?>