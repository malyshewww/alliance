// Инструкция по использованию
// gulp - дефолтная команда, которая собирает проект, запускает сервер и вотчер
// gulp watch - Запустить только вотчер
// gulp build - Полностью удалит содержимое папки dist и соберет все заново + минифицирует файлы css и js

// gulp ftp - Выгрузит содержимое папки dist на хост alexey-morozov.ru
// На хосте создастся папка соответствующая названию папки с проектом
// По умолчанию эта папка называется projectName, поэтому её нужно переименовывать




import pkg from 'gulp'
const { src, dest, parallel, series, watch: gulpWatch } = pkg

import dartSass from 'sass';
import gulpSass from 'gulp-sass';
import prefixer from 'gulp-autoprefixer';
import sourcemaps from 'gulp-sourcemaps';
import cleanCss from 'gulp-clean-css';
import cleanJs from 'gulp-minify';
import concat from 'gulp-concat';
import imagemin from 'gulp-imagemin';
import pngquant from 'imagemin-pngquant';
import jpegoptim from 'imagemin-jpegoptim';
import imgToPicture from 'gulp-webp-html-nosvg';
import webp from 'gulp-webp';
import newer from 'gulp-newer';
import clean from 'gulp-clean';
import browserSync from 'browser-sync';
import pug from 'gulp-pug';
import vinylFTP from 'vinyl-ftp';
import util from 'gulp-util';
const sass = gulpSass(dartSass);


// Пути
const path = {
	dist:{
		html: 'dist/',
		allFiles: 'dist/**/*.*',
		js: 'dist/js/',
		css: 'dist/css/',
		img: 'dist/img/',
		fonts: 'dist/fonts/'
	},
	src:{
		allFiles: 'src/**/*.*',
		pug: 'src/templates/**/*.pug',
		pages: 'src/templates/pages/*.pug',
		js: 'src/js/*.js',
		jsLibs: 'src/js/libs/**/*.js',
		style: 'src/style/**/*.scss',
		styleLibs: "src/style/libs/**/*.css",
		images: 'src/img/**/*.{jpg,jpeg,png,gif,webp}',
		otherImagesFiles: 'src/img/**/*.{svg,pdf,mp4}',
		fonts: 'src/fonts/**/*.*'
	},
};


function browsersync(){
	browserSync.init({
		server:{
			baseDir: "./dist",
		},
		host: 'localhost',
		port: 7777,
		notify: false,
	});
};


function pugDist(){
	return src([path.src.pages])
		.pipe(pug({
			pretty: true
		}))
		.pipe(imgToPicture())
		.pipe(dest(path.dist.html))
};


function stylesDist(){
	return src(path.src.style)
		.pipe(sourcemaps.init())
		.pipe(sass({
			outputStyle: 'expanded'
		}))
		.pipe(prefixer({
			overrideBrowserslist: ['last 3 versions', 'ie >= 10']
		}))
		.pipe(sourcemaps.write())
		.pipe(dest(path.dist.css))
};
function stylesLibs(){
	return src(path.src.styleLibs)
		.pipe(concat('libs.css'))
		.pipe(dest(path.dist.css))
};
function stylesMin(){
	return src(path.dist.css + "*.css")
		.pipe(cleanCss())
		.pipe(dest(path.dist.css))
};


function jsDist(){
	return src(path.src.js)
		.pipe(dest(path.dist.js))
};
function jsLibs(){
	return src(path.src.jsLibs)
		.pipe(concat('libs.js'))
		.pipe(dest(path.dist.js))
};
function jsMin(){
	return src(path.dist.js + "/*.js")
		.pipe(cleanJs({
			noSource: true,
			ext:{
				min:'.js',
			},
		}))
		.pipe(dest(path.dist.js))
};


function images(){
	return src(path.src.images)
		.pipe(newer(path.dist.img))
		.pipe(webp())
		.pipe(dest(path.dist.img))
		.pipe(src(path.src.images))
		.pipe(newer(path.dist.img))
		.pipe(imagemin([
			imagemin.gifsicle({interlaced: true}),
			jpegoptim({
				progressive: true,
				stripAll: true,
					max: 85
			}),
			pngquant(),
			imagemin.svgo({
				plugins: [
					{removeViewBox: true},
					{cleanupIDs: false}
				]
			})
		], {
			verbose: true
		}))
		.pipe(dest(path.dist.img))
		.pipe(src(path.src.otherImagesFiles))
		.pipe(newer(path.dist.img))
		.pipe(dest(path.dist.img))
};


function fontsDist(){
	return src(path.src.fonts)
		.pipe(dest(path.dist.fonts))
};


function cleanDist(){
	return src(path.dist.allFiles)
		.pipe(clean());
};


import * as nodePath from 'path';
const repositoryName = nodePath.basename(nodePath.resolve());
const connect = {
	host: '95.216.74.46', // Адрес FTP сервера
	user: 'wm84480', // Имя пользователя
	password: 'PoNAHsoeH', // Пароль
	parallel: 5 // Количество одновременных потоков
};
function uploadFtp(){
	connect.log = util.log;
	const ftpConnect = vinylFTP.create(connect);
	return src(`${path.dist.html}/**/*.*`)
		.pipe(ftpConnect.dest(`/domains/alexey-morozov.ru/public_html/${repositoryName}`))
};



function watcher(){
	gulpWatch([path.src.pug], { usePolling: true }, pugDist)
	gulpWatch([path.src.style], { usePolling: true }, stylesDist)
	gulpWatch([path.src.styleLibs], { usePolling: true }, stylesLibs)
	gulpWatch([path.src.js], { usePolling: true }, jsDist)
	gulpWatch([path.src.jsLibs], { usePolling: true }, jsLibs)
	gulpWatch([path.src.images], { usePolling: true }, images)
	gulpWatch([path.src.otherImagesFiles], { usePolling: true }, images)
	gulpWatch([path.src.fonts], { usePolling: true }, fontsDist)
	gulpWatch([path.src.allFiles], { usePolling: true }).on('change', browserSync.reload)
};

const watchLocal = series(parallel(pugDist, stylesDist, stylesLibs, jsDist, jsLibs, images, fontsDist), parallel(browsersync, watcher));
const watch = series(parallel(pugDist, stylesDist, stylesLibs, jsDist, jsLibs, images, fontsDist), parallel(watcher));
const build = series(cleanDist, parallel(pugDist, stylesDist, stylesLibs, jsDist, jsLibs, images, fontsDist), parallel(stylesMin, jsMin));
const ftp =  series(parallel(uploadFtp));



export { watchLocal, watch, build, ftp }
export default watchLocal;