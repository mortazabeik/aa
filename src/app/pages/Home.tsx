import { useState, useEffect } from "react";
import { Link } from "react-router";
import { GlassCard } from "../components/GlassCard";
import { ImageWithRoseFallback } from "../components/ImageWithRoseFallback";
import { getMartyrs, getSliders } from "../data/db";
import {
  ArrowLeft,
  Camera,
  Video,
  Heart,
  ChevronLeft,
  ChevronRight,
  Phone,
  Mail,
  Instagram,
  Send,
  Twitter,
  BookOpen,
  Calendar as CalendarIcon,
  MapPin,
} from "lucide-react";
import { motion, AnimatePresence } from "motion/react";

const RoseSVG = ({ className }: { className?: string }) => (
  <svg
    width="237pt"
    height="213pt"
    viewBox="0 0 237 213"
    version="1.1"
    xmlns="http://www.w3.org/2000/svg"
  >
    <g className="fill-rose-500/80 dark:fill-rose-800/80">
      <path
        opacity="1.00"
        d=" M 125.45 0.00 L 126.59 0.00 C 131.98 2.50 136.75 6.08 141.83 9.12 C 151.08 15.18 160.94 20.45 169.38 27.67 C 176.12 33.48 177.07 42.81 178.17 51.07 C 179.05 58.97 180.51 66.83 180.84 74.79 C 172.48 64.77 167.92 51.75 157.99 42.99 C 152.17 37.67 143.68 42.68 137.41 38.59 C 132.45 35.80 130.89 29.88 126.95 26.10 C 123.12 22.72 117.59 23.49 112.94 24.18 C 105.88 25.12 98.14 27.89 91.46 24.09 C 92.32 21.90 93.28 19.67 95.13 18.13 C 100.73 13.08 107.54 9.68 114.05 5.98 C 117.81 3.92 121.44 1.57 125.45 0.00 Z"
      />
      <path
        opacity="1.00"
        d=" M 45.23 16.23 C 49.34 10.54 55.84 6.35 62.96 5.99 C 67.13 5.94 71.63 7.60 73.80 11.37 C 77.53 17.25 76.56 24.82 73.85 30.90 C 72.19 34.70 68.52 36.92 65.56 39.60 C 62.87 42.36 62.35 46.41 61.91 50.07 C 60.75 62.03 61.96 74.11 60.27 86.03 C 60.13 87.22 59.65 88.33 59.24 89.45 C 56.13 85.59 53.60 81.31 50.41 77.50 C 47.41 73.57 43.29 70.57 40.72 66.28 C 35.10 57.34 33.99 46.14 35.99 35.93 C 37.40 28.71 41.04 22.18 45.23 16.23 Z"
      />
      <path
        opacity="1.00"
        d=" M 80.98 34.91 C 88.13 31.48 96.21 30.73 103.99 29.87 C 107.41 29.90 111.67 29.16 114.26 31.94 C 114.68 34.47 112.83 36.46 111.34 38.24 C 106.92 43.20 100.32 46.34 97.50 52.62 C 96.30 56.61 97.05 60.92 98.20 64.85 C 100.73 73.63 104.52 82.34 104.22 91.64 C 98.67 90.57 93.61 87.97 88.63 85.41 C 82.27 81.98 75.96 78.28 70.63 73.34 C 68.46 71.18 66.12 68.19 67.25 64.93 C 68.68 59.57 71.12 54.53 72.34 49.10 C 73.65 43.68 75.57 37.56 80.98 34.91 Z"
      />
      <path
        opacity="1.00"
        d=" M 113.14 44.16 C 122.32 41.99 132.15 45.80 138.60 52.42 C 142.35 56.22 144.76 61.87 142.95 67.18 C 140.70 62.92 138.33 58.65 134.51 55.57 C 134.41 58.18 133.94 61.07 131.67 62.71 C 128.68 65.37 124.49 65.15 120.77 65.37 C 120.75 64.47 120.71 63.57 120.68 62.66 C 124.99 63.94 130.16 59.66 128.80 55.07 C 128.07 51.39 123.86 49.94 120.54 50.40 C 115.12 52.01 113.81 60.77 118.88 63.55 C 121.50 68.67 128.43 68.46 133.38 69.17 C 126.70 72.12 118.12 71.33 112.77 66.12 C 110.27 63.82 108.61 60.38 109.19 56.94 C 109.46 54.55 111.10 52.70 112.37 50.77 C 109.17 52.22 107.11 55.03 106.00 58.29 C 103.12 52.51 106.98 45.46 113.14 44.16 Z"
      />
      <path
        opacity="1.00"
        d=" M 148.63 59.85 C 148.00 56.37 146.26 51.20 149.98 48.94 C 152.75 49.28 154.95 51.31 156.78 53.27 C 162.08 59.37 165.59 66.76 168.76 74.12 C 170.46 78.29 172.22 83.08 170.38 87.51 C 167.71 93.73 162.24 98.07 157.09 102.19 C 143.50 113.16 126.87 119.98 114.58 132.62 C 110.15 136.82 108.52 143.13 103.79 147.08 C 101.27 143.57 101.98 139.08 101.84 135.03 C 101.75 125.93 104.57 116.63 110.77 109.82 C 120.35 98.39 136.79 95.78 145.76 83.71 C 151.10 76.99 150.75 67.72 148.63 59.85 Z"
      />
      <path
        opacity="1.00"
        d=" M 220.41 59.43 C 226.26 56.90 230.69 51.76 237.00 50.14 L 237.00 53.52 C 235.32 58.73 232.18 63.35 230.86 68.69 C 228.02 79.54 227.17 90.85 227.68 102.03 C 227.87 111.92 226.55 122.12 221.87 130.97 C 217.18 139.79 208.57 146.08 199.09 148.91 C 189.57 151.78 179.61 154.28 169.59 153.09 C 169.38 147.05 171.06 141.18 172.68 135.42 C 175.87 124.80 179.76 114.17 180.24 102.99 C 179.66 92.55 181.66 81.81 186.54 72.53 C 188.91 67.86 194.04 65.58 199.00 64.95 C 206.32 63.99 213.64 62.48 220.41 59.43 Z"
      />
      <path
        opacity="1.00"
        d=" M 103.66 64.47 C 107.50 68.72 111.89 72.71 117.44 74.53 C 122.65 76.37 129.02 77.07 133.86 73.90 C 136.41 72.46 136.88 69.37 138.32 67.07 C 140.50 69.91 142.13 73.40 142.02 77.05 C 141.66 81.72 137.65 85.34 133.21 86.21 C 125.11 87.88 116.57 84.89 110.36 79.65 C 105.98 75.97 102.56 70.40 103.66 64.47 Z"
      />
      <path
        opacity="1.00"
        d=" M 0.00 75.82 C 2.30 72.13 6.99 71.80 10.88 71.35 C 21.80 70.49 33.74 71.34 42.77 78.22 C 53.66 86.35 58.51 99.94 60.75 112.87 C 62.61 125.35 65.29 137.85 70.52 149.39 C 73.28 155.22 77.86 161.02 76.85 167.81 C 76.35 170.62 73.14 170.11 71.02 170.00 C 60.38 168.80 50.04 165.68 40.02 162.00 C 34.06 159.57 27.92 157.15 22.90 153.03 C 19.21 150.22 19.19 145.18 18.59 141.00 C 17.10 128.71 18.20 116.29 16.91 104.00 C 16.36 98.55 15.07 92.81 11.20 88.71 C 7.68 84.58 1.85 82.44 0.00 76.98 L 0.00 75.82 Z"
      />
      <path
        opacity="1.00"
        d=" M 72.43 85.41 C 74.51 83.20 77.37 85.32 79.24 86.72 C 83.46 90.09 88.15 92.81 93.15 94.86 C 95.87 96.17 99.36 97.56 100.05 100.86 C 100.18 104.73 98.03 108.13 96.54 111.56 C 90.08 124.94 88.35 140.66 92.24 155.05 C 93.43 160.16 95.35 165.59 93.78 170.81 C 91.70 172.21 89.97 169.74 88.68 168.42 C 82.44 160.57 78.47 151.18 75.18 141.77 C 71.16 129.19 68.03 116.19 67.50 102.95 C 67.28 96.85 67.73 89.87 72.43 85.41 Z"
      />
      <path
        opacity="1.00"
        d=" M 157.23 113.17 C 159.98 109.28 162.28 104.58 166.88 102.56 C 169.86 106.31 170.78 111.24 170.20 115.92 C 168.93 128.15 164.94 139.93 160.23 151.23 C 156.86 158.70 153.22 166.41 146.92 171.88 C 141.38 176.86 134.80 181.76 127.02 181.81 C 120.40 181.91 113.81 179.97 107.96 176.96 C 102.61 174.10 98.85 168.04 100.02 161.87 C 101.69 152.30 109.25 145.16 116.83 139.77 C 125.41 133.46 136.00 130.79 144.72 124.72 C 149.38 121.47 154.10 118.00 157.23 113.17 Z"
      />
      <path
        opacity="1.00"
        d=" M 181.08 160.02 C 188.34 156.86 196.52 157.13 204.19 158.36 C 210.10 159.34 216.22 160.33 221.44 163.47 C 223.14 164.44 224.67 165.96 224.95 167.99 C 224.44 170.34 222.10 171.55 220.01 172.20 C 210.95 175.08 200.83 171.78 192.12 176.19 C 184.70 180.07 179.37 186.74 173.66 192.66 C 163.89 203.23 151.11 212.13 136.31 213.00 L 130.80 213.00 C 117.67 211.80 105.64 206.02 94.27 199.73 C 80.70 192.87 65.52 186.47 50.00 189.11 C 42.68 190.15 36.57 194.70 29.53 196.60 C 27.79 197.17 25.97 196.72 24.22 196.46 C 27.10 191.03 32.42 187.66 37.37 184.33 C 41.68 181.57 46.03 178.53 51.15 177.53 C 56.43 176.46 61.81 177.33 67.06 178.06 C 80.60 180.15 93.98 183.20 107.53 185.25 C 118.29 186.88 129.77 188.18 140.11 183.86 C 154.75 177.68 166.47 166.27 181.08 160.02 Z"
      />
    </g>
  </svg>
);

export function Home() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [sliderImages, setSliderImages] = useState(getSliders());

  useEffect(() => {
    document.title = "گلزار تربت - صفحه اصلی";
    setSliderImages(getSliders());
    const timer = setInterval(() => {
      setCurrentIndex((prev) => sliderImages.length > 0 ? (prev + 1) % sliderImages.length : 0);
    }, 6000);
    return () => clearInterval(timer);
  }, [sliderImages.length]);

  const blogPosts = [
    {
      id: 1,
      title: "برگزاری مراسم بزرگداشت شهدای گمنام",
      date: "۲۴ اردیبهشت ۱۴۰۳",
      excerpt:
        "مراسم بزرگداشت شهدای گمنام با حضور پرشور مردم و مسئولین در دانشگاه برگزار شد...",
      image: "slider/1.jpg",
    },
    {
      id: 2,
      title: "انتشار کتاب جدید «خاطرات اروند»",
      date: "۱۸ اردیبهشت ۱۴۰۳",
      excerpt:
        "کتاب جدیدی شامل خاطرات ناگفته رزمندگان از عملیات والفجر ۸ منتشر و روانه بازار شد...",
      image: "slider/1.jpg",
    },
    {
      id: 3,
      title: "فراخوان جشنواره شعر دفاع مقدس",
      date: "۱۰ اردیبهشت ۱۴۰۳",
      excerpt:
        "دهمین دوره جشنواره سراسری شعر دفاع مقدس با محوریت شهدای مدافع حرم آغاز به کار کرد...",
      image: "slider/1.jpg",
    },
  ];

  const handleNext = (e: React.MouseEvent) => {
    e.stopPropagation();
    if(sliderImages.length > 0) setCurrentIndex((prev) => (prev + 1) % sliderImages.length);
  };

  const handlePrev = (e: React.MouseEvent) => {
    e.stopPropagation();
    if(sliderImages.length > 0) setCurrentIndex((prev) => (prev - 1 + sliderImages.length) % sliderImages.length);
  };

  const getPersianMonthAndDay = () => {
    const today = new Date();
    // Using en-US-u-ca-persian to get a reliable format like "2/16/1403"
    const formatter = new Intl.DateTimeFormat('en-US-u-ca-persian', { month: 'numeric', day: 'numeric' });
    const parts = formatter.formatToParts(today);
    const month = parts.find(p => p.type === 'month')?.value || "";
    const day = parts.find(p => p.type === 'day')?.value || "";
    return { month, day };
  };

  const { month: currentMonth, day: currentDay } = getPersianMonthAndDay();
  
  const martyrsData = getMartyrs();
  let martyrsOfTheDay = martyrsData.filter(m => {
    const mMonth = parseInt(m.martyrdomMonth || "0");
    const mDay = parseInt(m.martyrdomDay || "0");
    return mMonth === parseInt(currentMonth) && mDay === parseInt(currentDay);
  }).map(m => ({
    ...m,
    name: `${m.firstName} ${m.lastName}`,
    imageUrl: m.profileImage || ""
  }));

  // Fallback to random martyrs if none found today so the UI isn't completely empty
  if (martyrsOfTheDay.length === 0) {
    martyrsOfTheDay = martyrsData.slice(0, 3).map(m => ({
      ...m,
      name: `${m.firstName} ${m.lastName}`,
      imageUrl: m.profileImage || ""
    }));
  } else {
    // Show only first 3 to keep UI tidy
    martyrsOfTheDay = martyrsOfTheDay.slice(0, 3);
  }

  return (
    <div className="space-y-20">
      {/* 3D Card Slider Section */}
      <section
        className="relative w-full py-10"
        style={{ perspective: "1000px" }}
      >
        <div className="relative h-[350px] md:h-[450px] w-full flex items-center justify-center transform-gpu">
          <AnimatePresence initial={false}>
            {sliderImages.length > 0 && sliderImages.map((slide, index) => {
              let position = index - currentIndex;

              if (position > sliderImages.length / 2) {
                position -= sliderImages.length;
              } else if (position < -sliderImages.length / 2) {
                position += sliderImages.length;
              }

              const translateDirection = -1;
              const x = position * 100 * translateDirection;
              const scale = 1 - Math.abs(position) * 0.15;
              const opacity = Math.abs(position) > 1.5 ? 0 : 1;
              const isActive = position === 0;

              return (
                  <motion.div
                  key={slide.id}
                  initial={{ opacity: 0 }}
                  animate={{
                    x: `${x}%`,
                    scale,
                    opacity,
                  }}
                  transition={{
                    duration: 0.6,
                    ease: "easeInOut",
                  }}
                  className={`absolute w-10/12 h-full rounded-[1.5rem] overflow-hidden cursor-pointer`}
                  style={{
                    transformStyle: "preserve-3d",
                  }}
                  drag="x"
                  dragConstraints={{ left: 0, right: 0 }}
                  dragElastic={0.2}
                  onDragEnd={(e, { offset }) => {
                    const swipeThreshold = 50;
                    if (offset.x > swipeThreshold) {
                      // Swipe right in RTL -> Next slide
                      setCurrentIndex((prev) => (prev + 1) % sliderImages.length);
                    } else if (offset.x < -swipeThreshold) {
                      // Swipe left in RTL -> Prev slide
                      setCurrentIndex((prev) => (prev - 1 + sliderImages.length) % sliderImages.length);
                    }
                  }}
                  onClick={() => {
                    if (!isActive) setCurrentIndex(index);
                  }}
                >
                  {/* Liquid reflection overlay */}
                  <div className="absolute inset-0 bg-gradient-to-tr from-white/20 via-transparent to-transparent z-10 pointer-events-none mix-blend-overlay" />

                  <img
                    src={slide.url}
                    alt={`اسلاید ${slide.id}`}
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700"
                    style={{
                      transform: isActive
                        ? "scale(1.05)"
                        : "scale(1)",
                    }}
                  />
                </motion.div>
              );
            })}
            {sliderImages.length > 0 && (
              <div className="absolute inset-0 z-30 flex justify-center w-full">
                <GlassCard className="absolute bottom-1 z-30 flex flex-col justify-end p-1 w-fit rounded-full ">
                  <div className="flex items-center justify-between">
                    <button
                      onClick={handlePrev}
                      className="p-1 rounded-full dark:text-white transition-all transform hover:scale-110"
                    >
                      <ChevronRight size={24} />
                    </button>
                    <div className="flex gap-2 bg-white/30 dark:bg-black/30 px-4 py-3 rounded-full border border-white/60 dark:border-white/20">
                      {sliderImages.map((_, idx) => (
                        <button
                          key={idx}
                          onClick={(e) => {
                            e.stopPropagation();
                            setCurrentIndex(idx);
                          }}
                          className={`h-2 rounded-full overflow-hidden transition-all duration-300 bg-gray-700/40 dark:bg-white/40 ${currentIndex === idx ? "w-8" : "w-2 hover:bg-white/70"}`}
                        >
                          <div
                            className={`transition-all float-left ${currentIndex === idx ? "duration-6000 w-8" : ""}  w-0 bg-gray-700 dark:bg-white h-2 `}
                          ></div>
                        </button>
                      ))}
                    </div>
                    <button
                      onClick={handleNext}
                      className="p-1 rounded-full  dark:text-white transition-all transform hover:scale-110"
                    >
                      <ChevronLeft size={24} />
                    </button>
                  </div>
                </GlassCard>
              </div>
            )}
          </AnimatePresence>
        </div>
      </section>

      {/* Intro Section - 50% Image / 50% Text */}
      <section>
        <GlassCard className="overflow-hidden p-0">
          <div className="flex flex-col lg:flex-row items-stretch min-h-[400px]">
            <div className="lg:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white/40 dark:bg-gray-900/40 backdrop-blur-sm z-10 relative">
              <h2 className="text-3xl md:text-4xl font-extrabold mb-6 bg-gradient-to-r from-blue-700 to-emerald-600 dark:from-blue-400 dark:to-emerald-400 bg-clip-text text-transparent">
                درباره سامانه گلزار تربت
              </h2>
              <p className="text-gray-700 dark:text-gray-300 text-lg leading-relaxed text-justify">
                این سامانه با هدف زنده نگه داشتن یاد و خاطره
                شهیدان گرانقدر تربت حیدریه و شهرستان های اطراف و
                دسترسی آسان به مشخصات این عزیزان راه‌اندازی شده
                است. ما بر این باوریم که ترویج فرهنگ ایثار و
                شهادت، ضامن بقای ارزش‌های والای انسانی است.
              </p>
              <div className="mt-8"></div>
            </div>

            <div className="lg:w-1/2 relative min-h-[300px] lg:min-h-full flex items-center justify-center dark:from-red-950/20 dark:to-rose-900/20 p-12">
              <div className="absolute inset-0" />

              <div className="relative z-10 w-full h-full max-h-[300px] flex items-center justify-center">
                <RoseSVG className="w-full h-full max-w-[250px] max-h-[250px]" />
              </div>
            </div>
          </div>
        </GlassCard>
      </section>

      {/* Martyrs of the Day */}
      <section className="space-y-8">
        <div className="flex justify-between items-end px-2 border-b border-gray-200 dark:border-gray-800 pb-4">
          <div>
            <h3 className="text-2xl md:text-3xl font-bold border-r-4 border-blue-500 pr-4 mb-2">
              شهدای روز
            </h3>
            <p className="text-gray-500 dark:text-gray-400 text-sm pr-5">
              آشنایی با قهرمانانی که امروز به شهادت رسیده‌اند
            </p>
          </div>
          <Link
            to="/search"
            className="hidden sm:flex items-center gap-2 text-sm font-medium bg-white/50 dark:bg-gray-800/50 px-4 py-2 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-white dark:hover:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 transition-all"
          >
            مشاهده همه
            <ArrowLeft size={16} />
          </Link>
        </div>

        <div className="flex overflow-x-auto hide-scrollbar snap-x snap-mandatory pb-6 -mx-4 px-4 md:mx-0 md:px-0 md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
          {martyrsOfTheDay.map((martyr) => (
            <GlassCard
              key={martyr.id}
              hoverEffect
              className="overflow-hidden flex flex-col max-w-[85vw] snap-center md:min-w-0 flex-shrink-0"
            >
              <div className="aspect-[3/4] overflow-hidden relative">
                <ImageWithRoseFallback
                  src={martyr.imageUrl}
                  alt={martyr.name}
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-4">
                  <h3 className="text-white font-bold text-lg leading-tight mb-1">
                    {martyr.name}
                  </h3>
                  <p className="text-gray-300 text-xs">
                    {martyr.birthPlace}
                  </p>
                </div>
              </div>
              <div className="p-4 flex-1 flex flex-col justify-between gap-4">
                <div className="text-sm text-gray-600 dark:text-gray-400 space-y-1 font-mono">
                  <div className="flex justify-between border-b border-gray-200 dark:border-gray-700/50 pb-1">
                    <span>ولادت:</span>
                    <span>{martyr.birthDate}</span>
                  </div>
                  <div className="flex justify-between pt-1">
                    <span>شهادت:</span>
                    <span className="text-red-500 dark:text-red-400">
                      {martyr.martyrdomDate}
                    </span>
                  </div>
                </div>
                <Link
                  to={`/martyr/${martyr.id}`}
                  className="w-full py-2 text-center text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg transition-colors"
                >
                  مشاهده پروفایل
                </Link>
              </div>
            </GlassCard>
          ))}
        </div>

        {/* Mobile View All Button */}
        <div className="sm:hidden flex justify-center mt-4">
          <Link
            to="/search"
            className="flex items-center justify-center gap-2 w-full bg-white dark:bg-gray-800 py-3 rounded-xl text-blue-600 dark:text-blue-400 shadow-sm border border-gray-200 dark:border-gray-700 font-medium"
          >
            مشاهده همه شهدا
            <ArrowLeft size={16} />
          </Link>
        </div>
      </section>

      {/* Blog/News Section */}
      <section className="space-y-8">
        <div className="flex justify-between items-end px-2 border-b border-gray-200 dark:border-gray-800 pb-4">
          <div>
            <h3 className="text-2xl md:text-3xl font-bold border-r-4 border-emerald-500 pr-4 mb-2">
              اخبار و مطالب
            </h3>
            <p className="text-gray-500 dark:text-gray-400 text-sm pr-5">
              تازه‌ترین رویدادها، مقالات و مطالب مرتبط با شهدا
            </p>
          </div>
          <Link
            to="#"
            className="hidden sm:flex items-center gap-2 text-sm font-medium bg-white/50 dark:bg-gray-800/50 px-4 py-2 rounded-xl text-emerald-600 dark:text-emerald-400 hover:bg-white dark:hover:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 transition-all"
          >
            وبلاگ
            <ArrowLeft size={16} />
          </Link>
        </div>

        <div className="flex overflow-x-auto hide-scrollbar snap-x snap-mandatory pb-6 -mx-4 px-4 md:mx-0 md:px-0 md:grid md:grid-cols-3 gap-6">
          {blogPosts.map((post) => (
            <GlassCard
              key={post.id}
              className="p-4 flex flex-col group max-w-[85vw] snap-center md:min-w-0 flex-shrink-0"
            >
              <div className="h-48 rounded-xl overflow-hidden mb-4 relative">
                <img
                  src={post.image}
                  alt={post.title}
                  className="w-full h-full object-cover transition-transform duration-700"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300" />
              </div>
              <div className="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 mb-2 font-medium">
                <CalendarIcon size={14} />
                <span>{post.date}</span>
              </div>
              <h4 className="font-bold text-lg mb-2 text-gray-900 dark:text-white line-clamp-2">
                {post.title}
              </h4>
              <p className="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                {post.excerpt}
              </p>
              <div className="mt-auto pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                <Link
                  to="#"
                  className="inline-flex items-center gap-1 text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:gap-2 transition-all"
                >
                  ادامه مطلب
                  <ArrowLeft size={14} />
                </Link>
              </div>
            </GlassCard>
          ))}
        </div>
      </section>

      {/* Call to Action: Submit Content */}
      <section>
        <GlassCard className="overflow-hidden p-0">
          <div className="relative p-8 md:p-14 bg-gradient-to-br from-blue-50/80 to-emerald-50/80 dark:from-blue-900/40 dark:to-emerald-900/40">
            {/* Decorative background shapes */}
            <div className="absolute top-0 right-0 w-64 h-64 bg-blue-400/20 dark:bg-blue-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div className="absolute bottom-0 left-0 w-64 h-64 bg-emerald-400/20 dark:bg-emerald-500/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

            <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
              <div className="flex-1 text-center md:text-right">
                <div className="inline-flex items-center gap-2 bg-white/60 dark:bg-gray-800/60 px-4 py-1.5 rounded-full mb-6 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-200/50 dark:border-gray-700/50">
                  <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                  شما هم رسانه باشید
                </div>
                <h3 className="text-3xl md:text-4xl font-extrabold mb-4 text-gray-900 dark:text-white">
                  آیا تصویر یا ویدیویی از شهدا دارید؟
                </h3>
                <p className="text-gray-700 dark:text-gray-300 mb-8 text-lg leading-relaxed">
                  شما می‌توانید با ارسال تصاویر، ویدیوها و
                  خاطرات خود از شهیدان، در تکمیل این گنجینه
                  معنوی ما را یاری کنید. آثار ارسالی پس از بررسی
                  با نام شما در سایت منتشر خواهد شد.
                </p>
                <Link
                  to="/submit"
                  className="inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 to-emerald-500 hover:from-blue-700 hover:to-emerald-600 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-[0_10px_30px_-10px_rgba(59,130,246,0.5)] hover:shadow-[0_15px_40px_-10px_rgba(59,130,246,0.6)] transform hover:-translate-y-1"
                >
                  <Camera size={22} />
                  <Video size={22} className="-mr-1" />
                  ارسال آثار و مستندات
                </Link>
              </div>
              <div className="flex-shrink-0 relative w-56 h-56 md:w-72 md:h-72 flex items-center justify-center">
                <div className="absolute inset-0 bg-gradient-to-tr from-blue-400/30 to-emerald-400/30 dark:from-blue-600/30 dark:to-emerald-600/30 rounded-[2.5rem] rotate-6 backdrop-blur-2xl animate-pulse shadow-xl border border-white/20"></div>
                <div className="absolute inset-0 bg-white/40 dark:bg-gray-800/40 rounded-[2.5rem] -rotate-3 backdrop-blur-xl shadow-lg border border-white/30"></div>
                <GlassCard className="w-36 h-36 md:w-48 md:h-48 rounded-[2rem] flex items-center justify-center relative z-10 shadow-2xl bg-white/60 dark:bg-gray-800/60 !border-white/50">
                  <div className="relative">
                    <Camera className="w-16 h-16 md:w-20 md:h-20 text-blue-600 dark:text-blue-400" />
                    <div className="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2 rounded-xl shadow-lg transform rotate-12">
                      <Video size={16} />
                    </div>
                  </div>
                </GlassCard>
              </div>
            </div>
          </div>
        </GlassCard>
      </section>

      {/* Contact Us Section */}
      <section className="pt-10 border-t border-gray-200/50 dark:border-gray-800/50">
        <GlassCard className="p-8 md:p-12">
          <div className="text-center mb-10">
            <h3 className="text-2xl md:text-3xl font-bold mb-4 text-gray-900 dark:text-white">
              ارتباط با ما
            </h3>
            <p className="text-gray-600 dark:text-gray-400">
              نظرات، پیشنهادات و آثار خود را از طریق راه‌های زیر
              با ما در میان بگذارید.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <a
              href="tel:02112345678"
              className="flex items-center gap-4 p-2 pr-3 rounded-3xl bg-white/40 dark:bg-gray-800/40 hover:bg-white/60 dark:hover:bg-gray-800/60 transition-colors border border-white/20 dark:border-gray-700/30 group"
            >
              <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                <Phone size={24} />
              </div>
              <div>
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  شماره تماس
                </p>
                <p
                  className="font-mono font-medium text-gray-900 dark:text-white"
                  dir="ltr"
                >
                  ۰۲۱ - ۱۲۳۴۵۶۷۸
                </p>
              </div>
            </a>

            <a
              href="mailto:info@yadman.ir"
              className="flex items-center gap-4 p-2 pr-3 rounded-3xl bg-white/40 dark:bg-gray-800/40 hover:bg-white/60 dark:hover:bg-gray-800/60 transition-colors border border-white/20 dark:border-gray-700/30 group"
            >
              <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                <Mail size={24} />
              </div>
              <div>
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  پست الکترونیک
                </p>
                <p className="font-mono font-medium text-gray-900 dark:text-white">
                  info@yadman.ir
                </p>
              </div>
            </a>

            <div className="flex items-center gap-4 p-2 pr-3 rounded-3xl bg-white/40 dark:bg-gray-800/40 border border-white/20 dark:border-gray-700/30 lg:col-span-1 md:col-span-2">
              <div className="w-12 h-12 flex items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                <MapPin size={24} />
              </div>
              <div>
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  آدرس دبیرخانه
                </p>
                <p className="font-medium text-gray-900 dark:text-white">
                  خراسان رضوی، تربت حیدریه، میدان شهدا، ساختمان
                  بنیاد شهید
                </p>
              </div>
            </div>
          </div>

          <div className="mt-8 flex justify-center gap-4">
            <a
              href="#"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-rose-100 hover:text-rose-600 transition-colors"
            >
              <Instagram size={20} />
            </a>
            <a
              href="#"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-blue-100 hover:text-blue-600 transition-colors"
            >
              <Send size={20} />
            </a>
            <a
              href="#"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-sky-100 hover:text-sky-600 transition-colors"
            >
              <Twitter size={20} />
            </a>
          </div>
        </GlassCard>
      </section>
    </div>
  );
}