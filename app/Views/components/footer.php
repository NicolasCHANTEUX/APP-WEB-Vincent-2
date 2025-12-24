<footer class="bg-primary-dark text-white py-8 px-6">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-start">
        <div class="mb-8 md:mb-0">
            <h3 class="text-2xl font-bold mb-4">KAYART</h3>
            <p><?= trans('footer_tagline') ?></p>
        </div>
        <div class="mb-8 md:mb-0">
            <h3 class="text-xl font-semibold mb-4"><?= trans('footer_contact_title') ?></h3>
            <p><?= trans('footer_contact_address') ?></p>
            <p><?= trans('footer_contact_phone') ?></p>
            <p><?= trans('footer_contact_email') ?></p>
        </div>
        <div>
            <h3 class="text-xl font-semibold mb-4"><?= trans('footer_social_title') ?></h3>
            <ul>
                <li><a href="#" class="hover:text-accent-gold"><?= trans('footer_social_instagram') ?></a></li>
                <li><a href="#" class="hover:text-accent-gold"><?= trans('footer_social_facebook') ?></a></li>
                <li><a href="#" class="hover:text-accent-gold"><?= trans('footer_social_linkedin') ?></a></li>
            </ul>
        </div>
    </div>
    <div class="text-center mt-8 pt-4 border-t border-gray-700">
        <p>&copy; <?= date('Y') ?> <?= trans('footer_copyright') ?></p>
    </div>
</footer>

