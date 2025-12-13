<footer class="bg-primary-dark text-white py-8 px-6 mt-16">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-start">
        <div class="mb-8 md:mb-0">
            <h3 class="text-2xl font-bold mb-4">KAYART</h3>
            <p><?= lang('Text.footer.tagline') ?></p>
        </div>
        <div class="mb-8 md:mb-0">
            <h3 class="text-xl font-semibold mb-4"><?= lang('Text.footer.contact.title') ?></h3>
            <p><?= lang('Text.footer.contact.address') ?></p>
            <p><?= lang('Text.footer.contact.phone') ?></p>
            <p><?= lang('Text.footer.contact.email') ?></p>
        </div>
        <div>
            <h3 class="text-xl font-semibold mb-4"><?= lang('Text.footer.social.title') ?></h3>
            <ul>
                <li><a href="#" class="hover:text-accent-gold"><?= lang('Text.footer.social.instagram') ?></a></li>
                <li><a href="#" class="hover:text-accent-gold"><?= lang('Text.footer.social.facebook') ?></a></li>
                <li><a href="#" class="hover:text-accent-gold"><?= lang('Text.footer.social.linkedin') ?></a></li>
            </ul>
        </div>
    </div>
    <div class="text-center mt-8 pt-4 border-t border-gray-700">
        <p>&copy; <?= date('Y') ?> <?= lang('Text.footer.copyright') ?></p>
    </div>
</footer>

