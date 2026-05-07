import { useForm } from "react-hook-form";
import { GlassCard } from "../components/GlassCard";
import {
  UploadCloud,
  CheckCircle,
  Image as ImageIcon,
  Video,
  User,
} from "lucide-react";
import { useState } from "react";
import { motion, AnimatePresence } from "motion/react";

type SubmitFormData = {
  fullName: string;
  email: string;
  martyrName: string;
  description: string;
};

export function Submit() {
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [selectedFileName, setSelectedFileName] = useState<
    string | null
  >(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm<SubmitFormData>();

  const onSubmit = (data: SubmitFormData) => {
    // Mock submission logic
    console.log("Submitted data:", data);
    setIsSubmitted(true);
    setTimeout(() => {
      setIsSubmitted(false);
      reset();
      setSelectedFileName(null);
    }, 5000);
  };

  const handleFileChange = (
    e: React.ChangeEvent<HTMLInputElement>,
  ) => {
    if (e.target.files && e.target.files.length > 0) {
      setSelectedFileName(e.target.files[0].name);
    }
  };

  return (
    <div className="max-w-3xl mx-auto space-y-8">
      <div className="text-center space-y-4">
        <h1 className="text-3xl md:text-4xl font-bold to-emerald-500 text-gray-800 dark:text-white inline-block">
          ارسال آثار و مستندات
        </h1>
        <p className="text-gray-600 dark:text-gray-400">
          تصاویر، ویدیوها و خاطرات خود از شهدای گرانقدر را با ما
          به اشتراک بگذارید تا در تکمیل این گنجینه سهیم باشید.
        </p>
      </div>

      <GlassCard className="p-6 md:p-10">
        <AnimatePresence mode="wait">
          {isSubmitted ? (
            <motion.div
              key="success"
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              className="flex flex-col items-center justify-center py-12 text-center"
            >
              <div className="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-6">
                <CheckCircle className="w-10 h-10 text-emerald-500" />
              </div>
              <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                با موفقیت ارسال شد
              </h3>
              <p className="text-gray-600 dark:text-gray-400 max-w-md">
                از همکاری شما سپاسگزاریم. مستندات شما پس از
                بررسی توسط تیم ما، در سایت منتشر خواهد شد.
              </p>
            </motion.div>
          ) : (
            <motion.form
              key="form"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onSubmit={handleSubmit(onSubmit)}
              className="space-y-6"
            >
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    نام و نام خانوادگی شما
                  </label>
                  <div className="relative">
                    <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <User className="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                      {...register("fullName", {
                        required: "نام الزامی است",
                      })}
                      className="block w-full pl-3 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                      placeholder="علی محمدی"
                    />
                  </div>
                  {errors.fullName && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.fullName.message}
                    </p>
                  )}
                </div>

                <div className="space-y-2">
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    ایمیل یا شماره تماس
                  </label>
                  <input
                    {...register("email", {
                      required:
                        "ایمیل یا شماره تماس الزامی است",
                    })}
                    className="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="جهت پیگیری‌های بعدی"
                  />
                  {errors.email && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.email.message}
                    </p>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  نام شهید (مرتبط با مستندات)
                </label>
                <input
                  {...register("martyrName", {
                    required: "نام شهید الزامی است",
                  })}
                  className="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="مثال: شهید همت"
                />
                {errors.martyrName && (
                  <p className="text-red-500 text-xs mt-1">
                    {errors.martyrName.message}
                  </p>
                )}
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  آپلود فایل (تصویر یا ویدیو)
                </label>
                <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl bg-white/30 dark:bg-gray-800/30 hover:bg-white/50 dark:hover:bg-gray-800/50 transition-colors">
                  <div className="space-y-1 text-center">
                    {selectedFileName ? (
                      <div className="flex flex-col items-center">
                        <CheckCircle className="mx-auto h-12 w-12 text-emerald-500 mb-2" />
                        <span className="text-sm text-gray-600 dark:text-gray-400">
                          {selectedFileName}
                        </span>
                      </div>
                    ) : (
                      <>
                        <UploadCloud className="mx-auto h-12 w-12 text-gray-400" />
                        <div className="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                          <label
                            htmlFor="file-upload"
                            className="relative cursor-pointer rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none"
                          >
                            <span>انتخاب فایل</span>
                            <input
                              id="file-upload"
                              name="file-upload"
                              type="file"
                              className="sr-only"
                              onChange={handleFileChange}
                              accept="image/*,video/*"
                            />
                          </label>
                          <p className="pl-1 mr-1">
                            یا فایل را اینجا رها کنید
                          </p>
                        </div>
                        <p className="text-xs text-gray-500 dark:text-gray-400 mt-2">
                          PNG, JPG, MP4 تا 50MB
                        </p>
                      </>
                    )}
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  توضیحات و خاطره
                </label>
                <textarea
                  {...register("description")}
                  rows={4}
                  className="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="اگر توضیحی درباره فایل ارسالی یا خاطره‌ای دارید بنویسید..."
                />
              </div>

              <button
                type="submit"
                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
              >
                ارسال مستندات
              </button>
            </motion.form>
          )}
        </AnimatePresence>
      </GlassCard>
    </div>
  );
}