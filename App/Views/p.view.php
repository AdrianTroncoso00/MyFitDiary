
<script>
    function mostrarMealPlan(fecha){
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                
            }
        }
        xmlhttp.open("GET", "http://myfitdiary.localhost:8081/MyFitDiary/App/Controllers/EdamamController.php")
    }

</script>

<div class="field">
    <label for="actividad">Semana</label>
    <select id="actividad" name="actividad" value="<?php echo isset($input['actividad']) ? $input['actividad'] : ''; ?>" required>
        <option value="" disabled selected>-</option>
        <?php foreach ($semana as $dia) { ?>
            <option value="<?php echo $dia?>"><?php echo $dia?></option>
            <?php
        }
        ?>
    </select>
</div>s