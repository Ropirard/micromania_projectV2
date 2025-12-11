<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<header class="mt-8 ml-8 bg-gray-200 h-20 flex items-center justify-between">
    <div class="flex items-center">
        <div class="flex flex-col ml-8">
            <a href="/admin" class="text-4xl font-bold text-gray-800 hover:text-gray-600 transition-colors"> <?= htmlspecialchars($title) ?><span class="text-xs">.com</span></a>
            <?php if(!isset($_SESSION['auth_user'])): ?>
                <a href="/login" class="text-xl text-blue-600 mt-2">Connexion</a>
            <?php endif; ?>
        </div>
        <?php if(!isset($_SESSION['auth_user'])): ?>
            <a href="/register" class="text-5xl text-red-600 ml-16">Inscription</a>
        <?php endif; ?>
    </div>
    
    <?php if(isset($_SESSION['auth_user'])): ?>
        <div class="flex items-center gap-6 mr-8">
            <span class="text-gray-700 font-medium">Bienvenue, <?= htmlspecialchars($user->firstname ?? 'Utilisateur') ?></span>
            <a href="/logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors inline-block cursor-pointer">
                Déconnexion
            </a>
        </div>
    <?php endif; ?>
</header>

<main class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-3 gap-6">
        <!-- Section gauche -->
        <section>
                <a href="/admin/orders" class="text-xl text-gray-900 font-semibold mb-4 hover:text-indigo-600 block"> Historique des commandes des utilisateurs</a>
                <a href="#" class="text-xl text-gray-500 mb-6 block"> Logs </a>
            <!-- Bloc supplémentaire -->
            <div class="mt-16">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Clicker</h3>
                <button id="clickerBtn" class="bg-gray-300 px-4 py-3">
                    Clique !
                </button>
                <span class="text-4xl font-semibold text-gray-800 mb-2" id="clickCounter">0</span>
            </div>
        </section>

        <!-- Section centre -->
        <section>
            <h1 class="text-xs">En ce qui concerne les modifications de jeux, clique plutôt</h1><a class="text-xs" href="/admin/edit/list">ici</a>
            <h1>Clique sur ce <a href="/admin/create">bouton</a> pour ajouter un jeu</h1>
            <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors h-screen w-80">
                Jeu ?
            </button>
        </section>

        <!-- Section droite -->
        <section>        
            <h2>Aujourd’hui, maman est morte. Ou peut-être hier, je ne sais pas. J’ai reçu un télégramme de l’asile : « Mère décédée.
Enterrement demain. Sentiments distingués. » Cela ne veut rien dire. C’était peut-être hier.
L’asile de vieillards est à Marengo, à quatre-vingts kilomètres d’ Alger. Je prendrai l’autobus à deux heures et
j’arriverai dans l’après-midi. Ainsi, je pourrai veiller et je rentrerai demain soir. J’ai demandé deux jours de congé à mon patron
et il ne pouvait pas me les refuser avec une excuse pareille. Mais il n’avait pas l’air content. Je lui ai même dit : « Ce n’est pas
de ma faute. » Il n’a pas répondu. J’ai pensé alors que je n’aurais pas dû lui dire cela. En somme, je n’avais pas à m’excuser.
C’était plutôt à lui de me présenter ses condoléances. Mais il le fera sans doute après-demain, quand il me verra en deuil. Pour
le moment, c’est un peu comme si maman n’était pas morte. Après l’enterrement, au contraire, ce sera une affaire classée et tout
aura revêtu une allure plus officielle.
J’ai pris l’autobus à deux heures. Il faisait très chaud. J’ai mangé au restaurant, chez Céleste, comme d’habitude. Ils
avaient tous beaucoup de peine pour moi et Céleste m’a dit : « On n’a qu’une mère. » Quand je suis parti, ils m’ont accompagné
à la porte. J’étais un peu étourdi parce qu’il a fallu que je monte chez Emmanuel pour lui emprunter une cravate noire et un
brassard. Il a perdu son oncle, il y a quelques mois.
J’ai couru pour ne pas manquer le départ. Cette hâte, cette course, c’est à cause de tout cela sans doute, ajouté aux
 cahots, à l’odeur d’essence, à la réverbération de la route et du ciel, que je me suis assoupi. J’ai dormi pendant presque tout le
trajet. Et quand je me suis réveillé, j’étais tassé contre un militaire qui m’a souri et qui m’a demandé si je venais de loin. J’ai dit
« oui » pour n’avoir plus à parler.
L’asile est à deux kilomètres du village. J’ai fait le chemin à pied. J’ai voulu voir maman tout de suite. Mais le
concierge m’a dit qu’il fallait que je rencontre le directeur. Comme il était occupé, j’ai attendu un peu. Pendant tout ce temps, le
 concierge a parlé et ensuite j’ai vu le directeur : il m’a reçu dans son bureau. C’était un petit vieux, avec la Légion d’honneur. Il
m’a regardé de ses yeux clairs. Puis il m’a serré la main qu’il a gardée si longtemps que je ne savais trop comment la retirer. I1
a consulté un dossier et m’a dit : « Mme Meursault est entrée ici il y a trois ans. Vous étiez son seul soutien. » J’ai cru qu’il me
reprochait quelque chose et j’ai commencé à lui expliquer. Mais il m’a interrompu : « Vous n’avez pas à vous justifier, mon
cher enfant. J’ai lu le dossier de votre mère. Vous ne pouviez subvenir à ses besoins. Il lui fallait une garde. Vos salaires sont
 modestes. Et tout compte fait, elle était plus heureuse ici. » J’ai dit : « Oui, monsieur le Directeur. » Il a ajouté : « Vous savez,
elle avait des amis, des gens de son âge. Elle pouvait partager avec eux des intérêts qui sont d’un autre temps. Vous êtes jeune
et elle devait s’ennuyer avec vous. »
C’était vrai. Quand elle était à la maison, maman passait son temps à me suivre des yeux en silence. Dans les premiers
jours où elle était à l’asile, elle pleurait souvent. Mais c’était à cause de l’habitude. Au bout de quelques mois, elle aurait pleuré
si on l’avait retirée de l’asile. Toujours à cause de l’habitude. C’est un peu pour cela que dans la dernière année je n’y suis
presque plus allé. Et aussi parce que cela me prenait mon dimanche – sans compter l’effort pour aller à l’autobus, prendre des
tickets et faire deux heures de route. </h2>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur clicker
    const clickerBtn = document.getElementById('clickerBtn');
    const clickCounter = document.getElementById('clickCounter');
    let count = 0;
    
    if (clickerBtn && clickCounter) {
        clickerBtn.addEventListener('click', function() {
            count++;
            clickCounter.textContent = count;
        });
    }
    
    // Vérifier si l'utilisateur est connecté (à adapter selon ton système)
    const isLoggedIn = <?= json_encode(isset($_SESSION['auth_user']) && $_SESSION['auth_user'] !== null) ?>;
    
    if (!isLoggedIn) {
        document.querySelectorAll('.auth-required').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const confirmed = confirm('Vous devez être connecté pour accéder à cette page.\n\nCliquez sur OK pour vous connecter, ou Annuler pour rester sur cette page.');
                
                if (confirmed) {
                    window.location.href = '/login';
                }
            });
        });
    }
});
</script>
</body>
</html>