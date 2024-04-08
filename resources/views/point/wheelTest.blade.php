@extends('layouts.master')

@section('css')
<style>
        body{
            background-color: #333;
        }

        .header{
            padding: 40px;
            color: #fff;
            margin: 0 auto;
            margin-bottom: 40px;
        }
        .header h1,p{
            text-align: center;
        }

        .wheel{
            display: flex;
            justify-content: center;
            position: relative;
        }
        .center-circle{
            width: 75px;
            height: 75px;
            border-radius: 60px;
            background-color: #fff;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
           
        }

        .center-circle h3{
            position: absolute;
            top: 50%;
           margin-left:auto;
           margin-right:auto;
           transform: translate(20%, -50%);
        }
        .triangle{
            width: 0; 
            height: 0; 
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent; 
            border-right: 30px solid red; 
            position: absolute;
            top: 50%;
            right: -300%;
            transform: translateY(-50%);
        }

    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-body">

            <div class="wheel">
                <canvas class="" id="canvas" width="500" height="500"></canvas>
                <div class="center-circle" onclick="spin()">
                    <h3>PRiM</h3>
                    <div class="triangle"></div>
                </div>
                
            </div>
            
            <div class="Winner"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>

        $(document).ready(function(){
            createWheel();
        });
        function randomColor(i) {
            if (i % 4 === 0) {
                // Even index, return dark purple
                return {r:255,g:179,b:102}; 
                // Dark purple: rgb(63, 0, 63)
            } else if( i%4 === 1 || i%4 === 3) {
                // Odd index, return light purple
                return { r: 238, g: 204, b: 255 }; // Light purple: rgb(221, 160, 221)
            } else {
                return {r:179,g:179,b:255};
            }
        }
        function toRad(deg){
            return deg * (Math.PI / 180.0);
        }
        function randomRange(min,max){
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
        function easeOutSine(x) {
            return Math.sin((x * Math.PI) / 2);
        }
        // get percent between 2 number
        function getPercent(input,min,max){
            return (((input - min) * 100) / (max - min))/100
        }
    </script>

    <script>
        const canvas = document.getElementById("canvas")
        const ctx = canvas.getContext("2d")
        const width = document.getElementById("canvas").width
        const height = document.getElementById("canvas").height

        const centerX = width/2
        const centerY = height/2
        const radius = width/2

        let items = @json($referral_code);

        let currentDeg = 0
        let step = 360/items.length
        let colors = []
        let itemDegs = {}

        for(let i = 0 ; i < items.length + 1;i++){
            colors.push(randomColor(i))
        }

        function createWheel(){
            items = @json($referral_code);
            console.log(items);
            step = 360/items.length
            colors = []
            for(let i = 0 ; i < items.length + 1;i++){
                colors.push(randomColor(i))
            }
            draw()
        }
        draw()

        function draw(){
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, toRad(0), toRad(360))
            ctx.fillStyle = `rgb(${33},${33},${33})`
            ctx.lineTo(centerX, centerY);
            ctx.fill()

            let startDeg = currentDeg;
            for(let i = 0 ; i < items.length; i++, startDeg += step){
                let endDeg = startDeg + step

                color = colors[i]
                let colorStyle = `rgb(${color.r},${color.g},${color.b})`

                ctx.beginPath();
                rad = toRad(360/step);
                ctx.arc(centerX, centerY, radius - 2, toRad(startDeg), toRad(endDeg))
                let colorStyle2 = `rgb(${color.r - 30},${color.g - 30},${color.b - 30})`
                ctx.fillStyle = colorStyle2
                ctx.lineTo(centerX, centerY);
                ctx.fill()

                ctx.beginPath();
                rad = toRad(360/step);
                ctx.arc(centerX, centerY, radius - 30, toRad(startDeg), toRad(endDeg))
                ctx.fillStyle = colorStyle
                ctx.lineTo(centerX, centerY);
                ctx.fill()

                // draw text
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(toRad((startDeg + endDeg)/2));
                ctx.textAlign = "center";
                if(color.r > 150 || color.g > 150 || color.b > 150){
                    ctx.fillStyle = "#000";
                }
                else{
                    ctx.fillStyle = "#fff";
                }
                ctx.font = 'bold 24px serif';
                ctx.fillText(items[i].code, 130, 10);
                ctx.restore();

                itemDegs[i] = 
                    {
                    "startDeg": startDeg,
                    "endDeg" : endDeg,
                    "code":items[i].code
                    }
                

                // check winner
                var code = items[i].code;
                
                //console.log(code,startDeg%360, endDeg % 360)
                if(startDeg%360 > 270 && endDeg % 360 <=270 ){
                   
                   $('.Winner').html('Winner: '+code);
                   //console.log('update',code,startDeg%360, endDeg % 360)
                }
                // if(startDeg%360 <= 360 && startDeg%360 > 270  && endDeg % 360 > 0 && endDeg%360 < 90 ){
                   
                //    $('.Winner').html('Winner: '+code);
                //    console.log('update',code,startDeg%360, endDeg % 360)
                // }
            }
        }
        

        let speed = 0
        let maxRotation =360* 5 + randomRange(0,360);
        let pause = false
        function animate(){
            if(pause){
                return
            }
            speed = easeOutSine(getPercent(currentDeg ,maxRotation ,0)) * 20
            if(speed < 0.1){
                speed = 0
                pause = true
                console.log($('.Winner').html());
                //spin();
            }
            currentDeg += speed
            draw()
            window.requestAnimationFrame(animate);
        }
        
        function randomItem(){
            return Math.random() + 0.5;
        }
        function spin(){
            if(speed != 0){
                return
            }
            items.sort(() => Math.random() - 0.5);
            maxRotation = 360* 5 + randomRange(-180,360);
            currentDeg =0;
           // createWheel()
            draw();
            //alert('hello');
           // maxRotation = (360 * 6) - itemDegs[0].endDeg + 10
            itemDegs = {}
            console.log("max",maxRotation,maxRotation%360)
            //console.log(itemDegs);
            pause = false
            window.requestAnimationFrame(animate);
        }

    </script>
@endsection