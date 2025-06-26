<?php 
    if(isset($_SESSION['name'])) {
        ?>
            <main class="accaunts">
                <?php
                    if(checkRole(['admin', 'moderator'])) {
                        if (isset($_GET['closed'])) {
                            ?>
                                <h4 class="tablet-title">Closed Suggests</h4>
                                <div id="result"></div>

                                <script>
                                    const xhttp = new XMLHttpRequest();

                                    function loadFn(num) {
                                        xhttp.onload = function() {
                                            document.getElementById("result").innerHTML = this.responseText;
                                        }
                                        xhttp.open("GET", "page\\includes\\page_list_gen.php?page=suggest&closed&count=" + num, true);
                                        xhttp.send();
                                    }
                                    loadFn(0);
                                </script>
                            <?php
                        } else {
                            ?>
                                <h4 class="tablet-title">Suggests</h4>
                                <div id="result"></div>

                                <script>
                                    const x_http = new XMLHttpRequest();

                                    function loadFn(num) {
                                        x_http.onload = function() {
                                            document.getElementById("result").innerHTML = this.responseText;
                                        }
                                        x_http.open("GET", "page\\includes\\page_list_gen.php?page=suggest&count=" + num, true);
                                        x_http.send();
                                    }
                                    loadFn(0);
                                </script>
                            <?php 
                        }
                    } else {
                        ?>
                        
                        <?php
                    }
                ?>
            </main>
            <style>

            </style>
        <?php
    } else { 
        global $mainUrl;
        header("Location: ".$mainUrl."?page=login");
        exit;
    }
?>