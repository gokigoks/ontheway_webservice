<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2015-12-13
 * Time: 2:44 PM
 */
?>
<!DOCTYPE html>
<html>
<head>
    <link href="{{ asset('/css/gmap.css') }}" rel="stylesheet">
</head>
<body>


<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry"></script>
<div id="map_canvas" style="border: 2px solid #3872ac;"></div>
</body>
<script>
    var map;
    var bounds = new google.maps.LatLngBounds();

    function initialize() {
        map = new google.maps.Map(
                document.getElementById("map_canvas"), {
                    center: new google.maps.LatLng(34, 108),
                    zoom: 13,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

        var jsonData = {
            "overview_polyline": {
                "points": "yjdbAgkpwVw}cH~g~@JOuL~tBy{DrdNorF~vLkq@zqDwHbN}KvQymAjk@aMzPi[jvAgxBjoBwcAbxAwd@fkAaq@za@}d@fy@ya@zNmc@zq@_nA{dAqRwl@{ZsYuCkTyl@{]eMsg@s_@cX_sBzr@a[fOubArlAiH~s@eeArsAgo@fSwdA_[oi@g_@o_Agh@ax@fC}wAsp@wsAsCci@~Qcs@rk@kw@ra@cd@kiA{pAFit@wsBmgA{p@mwEsiBgvCgf@_g@rYcYrBwoAoPm`@rQqFfvErWzi@zBb]Wr~@kk@nk@on@zsBzAnkAlRnk@vZvc@xEfWwKjnAbDrnB_hA~v@Znm@iFnhAd}@bxA|Xv_AjN~IbCfpAbg@fy@os@~jBoPnjA|AzfBcRvb@oIflAxCf_@kLz\qu@~wAuu@by@cx@jt@gS~j@ekAfGuhAsHy\~M}o@f@ox@b^iZGcaBzw@a^~JsqA{A}gBoI}fAon@}_AnBabAod@eg@~C_q@zrAwy@zq@uNju@uj@rb@mg@fuAhPjWj[nM`QzWyEzkA}y@byBqD~iAwf@rk@iBjn@a^~OoXzh@egAj{@}~A~d@ud@~m@rEb_D{e@bj@cbAvzBmXjYgYbh@yrAn\{_@Jum@~LkK~k@gJcQfJbQu\bw@wzDzsF{Kr}A_rBzyDuLfLmYr{@eoCfc@kzA{CklA_Mqb@bIwiCzu@qwCrPcToQ{p@~Jwn@ny@e_BnpCul@r}@_xAz~Aab@j_@us@~`@y}@vs@yp@vN}KrLy}DfoBmdA{D{v@~O_|@bdAacAbR_O~sAjIbd@|Tvh@pPbjAxDjv@{Pna@}TbKo@ju@vIzFjhB~^xbCv~Bbl@z{@jo@v`@bUvdBi]z`Acg@bq@kZnbBmIv|@aIzcBcjAzr@wyS~aKwGfTe|@jZop@fNy^jd@ge@bHqd@sQgt@nw@oiAwLcc@fj@a[KgJzm@y^bg@y_@zYyC~e@dRviAyBnm@mx@j`BjCrzA}H~n@pX~y@hOjYb@vd@os@~v@yGbz@c\bOoWbg@Mjm@xS~q@wFvw@qSne@s~Av_Aou@gEeYns@mc@rL_Tz_A`a@~k@pIbp@vp@bi@gXzk@cPbM{LfeA_c@r`Aa{@b{@}a@z`Ao@zuAo_A~k@oXv}@qdArMq`@z[o|Afn@_U~^opAjx@sFnk@dDriAaAzZnLnNp@nb@kUfh@cjAzTiOvrA}a@RyKnFsd@~m@mI~s@Ore@u_@rnAqw@bh@_o@fHgd@fk@kPjq@ofA_UgjAnX_\bUq`AsImGz]ua@vp@rBrp@aUrqAlHjNdm@bKmEvfAfC~q@mrAnjAso@r}AcgBjp@}LfrBGv^y_@bx@sS~QkLbp@oSjSmKbx@u]fY~v@nn@b\zl@z}@ft@tg@fz@ng@cAtaA_Ih[jx@leAgJfy@f]tk@nn@pa@o[fc@fOlsAWx]sd@``@gJjuAn_A~@zF_[b`B{T~U_DzkA`o@f@hl@z^lk@cZvbB^nFrX`z@rR`f@bQfKzUni@jSvXbZjWzlAqUjmBk[bp@s@fZm[be@cHj{@yXbz@o^j]{Are@wq@zlAvGfJxLjtAxLbr@mXjgCgt@bp@cQfmAhPnWfDzh@}u@bUwb@ndAuZrbCrAba@gOza@iGvd@ul@z{Asa@vr@_R~jAu_@jbA{|@ra@o`@b_BlHzaAiP~s@oGjDol@zzAoVj\sf@rjAf`@rWaM~v@xGbc@l]nc@sM~bAuk@rdBuInm@hTz}@}Eb`@hQfUrHrgC|nA~iCzMvv@cEzm@kIfSwp@jaA}Iv_BfIz[eFjwAzf@~pCrS~WgAjkAlDrNlt@nz@jJn]jG~}AxKf`@|p@rrAkLbjJf@r[{GvaC}q@n`@q\n_@nl@np@tKvr@tEvv@xMfu@hc@zwA~mArhApmAfjBfGzaFnIz`BKvp@_Ur{Byj@~nB{zAjgGuNnJ_b@fx@ckB~Pat@_c@u}FkgAkrBS}iBjyB{j@bkBnN~e@xk@z_EoJz`Dp^bpAlE~}@_yAz}BkdAzvA_k@vcAsYr_@sMjg@sp@n_A_KrZgx@zg@muBza@ijAbE}{AgRqeDnGyg@sUsyIfsE}rHb{C{LnHydD~mCq}Bb~@kRjDszFnj@kpAoFufA{_@ovLrAcWnF{sEjnBucBzz@bQrjA"
            } //
        };
        var path = google.maps.geometry.encoding.decodePath(jsonData.overview_polyline.points);
        //console.log(path);
        for (var i = 0; i < path.length; i++) {
            bounds.extend(path[i]);
        }

        var polyline = new google.maps.Polyline({
            path: path,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map
            // strokeColor: "#0000FF",
            // strokeOpacity: 1.0,
            // strokeWeight: 2
        });
        polyline.setMap(map);
        map.fitBounds(bounds);

    }

    google.maps.event.addDomListener(window, "load", initialize);
</script>
<script src="http://jsconsole.com/remote.js?CB6692C-A4D6-4518-9367-58876AF65B3D"></script>
</html>
