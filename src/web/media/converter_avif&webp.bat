@echo off
setlocal enabledelayedexpansion

rem A script helye
set "script_path=%~dp0"

rem Végigmegy a script mappájában található összes almappán
for /r "%script_path%" %%d in (.) do (
    pushd "%%d"
    rem Keres minden .jpg fájlt az aktuális mappában
    for %%i in (*.jpg) do (
        set "filename=%%~ni"
        rem Konvertálás AVIF formátumba
        ffmpeg -i "%%i" -c:v libaom-av1 -crf 30 -b:v 0 "!filename!.avif"
        rem Konvertálás WebP formátumba
        ffmpeg -i "%%i" -c:v libwebp -lossless 0 -q:v 80 "!filename!.webp"
    )
    popd
)

echo Konverzió befejezve.
pause
