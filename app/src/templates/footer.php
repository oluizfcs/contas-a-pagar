        </div>
    </div>
<script src="<?= $_ENV['BASE_URL'] ?>/src/public/js/charcounter.js"></script>
<script src="<?= $_ENV['BASE_URL'] ?>/src/public/js/sorttable.js"></script>
<script>
$(document).ready(function () {
    $('.select2').select2({
        language: {
            noResults: function () {
                return "Nenhuma opção encontrada."
            }
        }
    });
    
    $('.money').maskMoney({
        prefix: 'R$ '
    }).trigger('mask.maskMoney');
});
</script>
</body>
</html>