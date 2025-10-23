"use client";
import Link from "next/link";
import { useState } from "react";
import { useI18n } from "./i18n/I18nContext";

export default function Home() {
  const { t, lang, setLang } = useI18n();
  const [mobileOpen, setMobileOpen] = useState(false);
  
  return (
    <div className="min-h-screen bg-gradient-to-br from-rose-50 via-orange-50 to-amber-50">
      {/* Navigation */}
      <nav className="fixed top-0 w-full bg-white/80 backdrop-blur-md z-50 border-b border-rose-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center space-x-2">
              <span className="text-3xl">🍽️</span>
              <span className="text-2xl font-bold bg-gradient-to-r from-rose-600 to-orange-600 bg-clip-text text-transparent">
                QrMenu
              </span>
            </div>
            {/* Desktop nav */}
            <div className="hidden md:flex items-center space-x-8">
              <a href="#features" className="text-gray-700 hover:text-rose-600 transition">{t("features")}</a>
              <a href="#how-it-works" className="text-gray-700 hover:text-rose-600 transition">{t("howItWorks")}</a>
              <a href="#contact" className="text-gray-700 hover:text-rose-600 transition">{t("contact")}</a>
              
              {/* Language Switcher */}
              <div className="flex items-center space-x-2">
                <button
                  onClick={() => setLang("hy")}
                  className={`text-xl ${lang === "hy" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="Հայերեն"
                >
                  🇦🇲
                </button>
                <button
                  onClick={() => setLang("en")}
                  className={`text-xl ${lang === "en" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="English"
                >
                  🇬🇧
                </button>
                <button
                  onClick={() => setLang("ru")}
                  className={`text-xl ${lang === "ru" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="Русский"
                >
                  🇷🇺
                </button>
              </div>
              
              <Link 
                href="/table/1" 
                className="bg-gradient-to-r from-rose-600 to-orange-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition"
              >
                {t("tryLiveDemo")}
              </Link>
            </div>

            {/* Mobile hamburger */}
            <div className="md:hidden">
              <button
                aria-label="Toggle menu"
                onClick={() => setMobileOpen((v) => !v)}
                className="inline-flex items-center justify-center w-10 h-10 rounded-md border border-rose-200 text-rose-700 hover:bg-rose-50 active:bg-rose-100 transition"
              >
                {/* Hamburger icon */}
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  strokeWidth={1.5}
                  stroke="currentColor"
                  className="w-6 h-6"
                >
                  {mobileOpen ? (
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                  ) : (
                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                  )}
                </svg>
              </button>
            </div>
          </div>
        </div>
      </nav>

      {/* Mobile menu panel */}
      {mobileOpen && (
        <div className="md:hidden fixed top-16 inset-x-0 z-40 bg-white/95 backdrop-blur border-b border-rose-100 shadow-sm">
          <div className="px-4 py-4 space-y-4">
            <div className="flex flex-col space-y-3">
              <a href="#features" onClick={() => setMobileOpen(false)} className="text-gray-700 hover:text-rose-600 transition">{t("features")}</a>
              <a href="#how-it-works" onClick={() => setMobileOpen(false)} className="text-gray-700 hover:text-rose-600 transition">{t("howItWorks")}</a>
              <a href="#contact" onClick={() => setMobileOpen(false)} className="text-gray-700 hover:text-rose-600 transition">{t("contact")}</a>
            </div>
            <div className="border-t border-rose-100 pt-4">
              <div className="flex items-center gap-3">
                <button
                  onClick={() => { setLang("hy"); setMobileOpen(false); }}
                  className={`text-2xl ${lang === "hy" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="Հայերեն"
                >🇦🇲</button>
                <button
                  onClick={() => { setLang("en"); setMobileOpen(false); }}
                  className={`text-2xl ${lang === "en" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="English"
                >🇬🇧</button>
                <button
                  onClick={() => { setLang("ru"); setMobileOpen(false); }}
                  className={`text-2xl ${lang === "ru" ? "opacity-100" : "opacity-40 hover:opacity-70"} transition`}
                  title="Русский"
                >🇷🇺</button>
                <Link
                  href="/table/1"
                  onClick={() => setMobileOpen(false)}
                  className="ml-auto bg-gradient-to-r from-rose-600 to-orange-600 text-white px-4 py-2 rounded-full text-sm font-semibold hover:shadow-lg transition"
                >
                  {t("tryLiveDemo")}
                </Link>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Hero Section */}
      <section className="pt-32 pb-20 px-4">
        <div className="max-w-7xl mx-auto text-center">
          <div className="inline-block mb-4 px-4 py-2 bg-rose-100 rounded-full text-rose-700 text-sm font-semibold">
            🚀 {t("modernRestaurantSystem")}
          </div>
          <h1 className="text-5xl md:text-7xl leading-[1.2] md:leading-[1.2] font-bold mb-6 bg-gradient-to-r from-rose-600 via-orange-600 to-amber-600 bg-clip-text text-transparent">
            {t("scanOrderEnjoy")}
          </h1>
          <p className="text-xl md:text-2xl text-gray-700 mb-8 max-w-3xl mx-auto">
            {t("transformRestaurant")}
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link 
              href="/table/1"
              className="bg-gradient-to-r from-rose-600 to-orange-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-2xl transition transform hover:scale-105"
            >
              {t("tryLiveDemo")}
            </Link>
            <a 
              href="#contact"
              className="bg-white text-rose-600 px-8 py-4 rounded-full text-lg font-semibold border-2 border-rose-600 hover:bg-rose-50 transition"
            >
              {t("getStarted")}
            </a>
          </div>
          
          {/* Stats */}
          <div className="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
            <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-rose-100">
              <div className="text-4xl font-bold text-rose-600 mb-2">1000+</div>
              <div className="text-gray-600">{t("dailyOrders")}</div>
            </div>
            <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-orange-100">
              <div className="text-4xl font-bold text-orange-600 mb-2">50K+</div>
              <div className="text-gray-600">{t("happyCustomers")}</div>
            </div>
            <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-amber-100">
              <div className="text-4xl font-bold text-amber-600 mb-2">99.9%</div>
              <div className="text-gray-600">{t("uptime")}</div>
            </div>
            <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-rose-100">
              <div className="text-4xl font-bold text-rose-600 mb-2">3</div>
              <div className="text-gray-600">{t("languages")}</div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" className="py-20 px-4 bg-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              {t("whyChooseQrMenu")}
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              {t("everythingYouNeed")}
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {/* Feature 1 */}
            <div className="bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl p-8 border border-rose-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">📱</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("qrCodeOrdering")}</h3>
              <p className="text-gray-600">
                {t("qrCodeOrderingDesc")}
              </p>
            </div>

            {/* Feature 2 */}
            <div className="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-8 border border-orange-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">💳</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("multiplePayments")}</h3>
              <p className="text-gray-600">
                {t("multiplePaymentsDesc")}
              </p>
            </div>

            {/* Feature 3 */}
            <div className="bg-gradient-to-br from-amber-50 to-rose-50 rounded-2xl p-8 border border-amber-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">🔔</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("realtimeNotifications")}</h3>
              <p className="text-gray-600">
                {t("realtimeNotificationsDesc")}
              </p>
            </div>

            {/* Feature 4 */}
            <div className="bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl p-8 border border-rose-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">🌍</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("multiLanguage")}</h3>
              <p className="text-gray-600">
                {t("multiLanguageDesc")}
              </p>
            </div>

            {/* Feature 5 */}
            <div className="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-8 border border-orange-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">📊</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("analyticsDashboard")}</h3>
              <p className="text-gray-600">
                {t("analyticsDashboardDesc")}
              </p>
            </div>

            {/* Feature 6 */}
            <div className="bg-gradient-to-br from-amber-50 to-rose-50 rounded-2xl p-8 border border-amber-100 hover:shadow-xl transition">
              <div className="text-5xl mb-4">⚡</div>
              <h3 className="text-2xl font-bold mb-3 text-gray-900">{t("lightningFast")}</h3>
              <p className="text-gray-600">
                {t("lightningFastDesc")}
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section id="how-it-works" className="py-20 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              {t("howItWorks")}
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              {t("getStartedInSteps")}
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-rose-500 to-orange-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                1
              </div>
              <h3 className="text-xl font-bold mb-2 text-gray-900">{t("scanQrCode")}</h3>
              <p className="text-gray-600">{t("scanQrCodeDesc")}</p>
            </div>

            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                2
              </div>
              <h3 className="text-xl font-bold mb-2 text-gray-900">{t("browseMenu")}</h3>
              <p className="text-gray-600">{t("browseMenuDesc")}</p>
            </div>

            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-amber-500 to-rose-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                3
              </div>
              <h3 className="text-xl font-bold mb-2 text-gray-900">{t("placeOrderStep")}</h3>
              <p className="text-gray-600">{t("placeOrderStepDesc")}</p>
            </div>

            <div className="text-center">
              <div className="w-20 h-20 bg-gradient-to-br from-rose-500 to-orange-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                4
              </div>
              <h3 className="text-xl font-bold mb-2 text-gray-900">{t("payAndEnjoy")}</h3>
              <p className="text-gray-600">{t("payAndEnjoyDesc")}</p>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section id="contact" className="py-20 px-4 bg-gradient-to-r from-rose-600 to-orange-600">
        <div className="max-w-4xl mx-auto text-center text-white">
          <h2 className="text-4xl md:text-5xl font-bold mb-6">
            {t("readyToTransform")}
          </h2>
          <p className="text-xl mb-8 opacity-90">
            {t("joinHundreds")}
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-12">
            <a 
              href="mailto:contact@qrmenu.am"
              className="bg-white text-rose-600 px-8 py-4 rounded-full text-lg font-semibold hover:shadow-2xl transition"
            >
              📧 karen.kgirgoryan@gmail.com
            </a>
            <a 
              href="tel:+37412345678"
              className="bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-full text-lg font-semibold border-2 border-white hover:bg-white/30 transition"
            >
              📱 +374 77 316 566
            </a>
          </div>
          <p className="text-white/80">
            📍 Yerevan, Armenia
          </p>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-900 text-white py-12 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-4 gap-8 mb-8">
            <div>
              <div className="flex items-center space-x-2 mb-4">
                <span className="text-3xl">🍽️</span>
                <span className="text-2xl font-bold">QrMenu</span>
              </div>
              <p className="text-gray-400">
                Modern restaurant ordering system for the digital age.
              </p>
            </div>
            
            <div>
              <h4 className="font-bold mb-4">{t("product")}</h4>
              <ul className="space-y-2 text-gray-400">
                <li><a href="#features" className="hover:text-white transition">{t("features")}</a></li>
                <li><a href="#how-it-works" className="hover:text-white transition">{t("howItWorks")}</a></li>
                <li><Link href="/table/1" className="hover:text-white transition">Demo</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="font-bold mb-4">{t("company")}</h4>
              <ul className="space-y-2 text-gray-400">
                <li><a href="#contact" className="hover:text-white transition">{t("contact")}</a></li>
                <li><a href="#" className="hover:text-white transition">{t("aboutUs")}</a></li>
                <li><a href="#" className="hover:text-white transition">{t("careers")}</a></li>
              </ul>
            </div>
            
            <div>
              <h4 className="font-bold mb-4">{t("legal")}</h4>
              <ul className="space-y-2 text-gray-400">
                <li><a href="#" className="hover:text-white transition">{t("privacyPolicy")}</a></li>
                <li><a href="#" className="hover:text-white transition">{t("termsOfService")}</a></li>
                <li><a href="#" className="hover:text-white transition">{t("cookiePolicy")}</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-gray-800 pt-8 text-center text-gray-400">
            <p>© 2025 QrMenu. {t("allRightsReserved")}</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
