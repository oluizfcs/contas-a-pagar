        </div>
    </div>
<script src="<?= $_ENV['BASE_URL'] ?>/js/charcounter.js"></script>
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

    document.getElementById("content").focus();
});
</script>
</body>
</html>