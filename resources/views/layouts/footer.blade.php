<!-- <footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                © <script>document.write(new Date().getFullYear())</script> Veltrix<span class="d-none d-sm-inline-block"> - Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand.</span>
            </div>
        </div>
    </div>
</footer> -->

@auth
    <footer class="footer">
        © Copyrights <script>document.write(new Date().getFullYear())</script> All rights reserved | PRiM
    </footer>
@else
    <footer class="footer-guest">
        © Copyrights <script>document.write(new Date().getFullYear())</script> All rights reserved | PRiM
    </footer>
@endauth