import React from 'react';
import { Mail, Briefcase, User, MapPin } from 'lucide-react';

// Main component, must be the default export
const App = () => {
    // Custom colors defined as variables for readability in JSX (mimicking the original color intent)
    const PRIMARY_ACCENT = 'text-blue-400'; // Brighter blue for dark mode
    const SECONDARY_HIGHLIGHT = 'text-cyan-400'; // Bright cyan highlight
    const BG_DARK_PRIMARY = 'bg-gray-900'; // Main dark background
    const BG_DARK_CARD = 'bg-gray-800'; // Card background
    const TEXT_LIGHT = 'text-gray-100'; // Main text color
    const TEXT_SUBTLE = 'text-gray-400'; // Secondary text color

    // Component for the "About Me" Card
    const AboutCard = () => (
        <section className={`md:col-span-1 ${BG_DARK_CARD} p-6 rounded-xl shadow-2xl border-t-4 border-cyan-400 transition-all duration-300 hover:shadow-cyan-400/30`}>
            <h2 className={`text-2xl font-bold ${PRIMARY_ACCENT} mb-4 flex items-center`}>
                <User className="w-6 h-6 mr-2" />
                Who Am I?
            </h2>
            <ul className="space-y-3">
                <li className={TEXT_LIGHT}>
                    <strong className="font-semibold text-gray-200 flex items-center">
                        <MapPin className="w-4 h-4 mr-2" /> Location:
                    </strong>
                    <span className={`ml-6 block sm:inline ${TEXT_SUBTLE}`}>From the beautiful Algarve, Portugal.</span>
                </li>
                <li className={TEXT_LIGHT}>
                    <strong className="font-semibold text-gray-200">
                        Age:
                    </strong>
                    <span className={`ml-2 block sm:inline ${TEXT_SUBTLE}`}>25 Years Old.</span>
                </li>
                <li className={TEXT_LIGHT}>
                    <strong className="font-semibold text-gray-200">
                        Hobbies:
                    </strong>
                    <span className={`ml-2 block sm:inline ${TEXT_SUBTLE}`}>I enjoy reading about history and, of course, practical tech projects!</span>
                </li>
            </ul>
        </section>
    );

    // Component for the "Skills/Journey" Card
    const JourneyCard = () => (
        <section className={`md:col-span-2 ${BG_DARK_CARD} p-6 rounded-xl shadow-2xl border-t-4 border-blue-400 transition-all duration-300 hover:shadow-blue-400/30`}>
            <h2 className={`text-2xl font-bold ${PRIMARY_ACCENT} mb-4 flex items-center`}>
                <Briefcase className="w-6 h-6 mr-2" />
                Current Focus: Server & Networking
            </h2>
            <p className={`${TEXT_SUBTLE} leading-relaxed`}>
                I am actively on a learning journey, dedicating my time to improve my practical server and networking skills. This isn't just about reading books—it's about getting my hands dirty!
            </p>
            <p className={`mt-3 leading-relaxed font-medium ${TEXT_LIGHT}`}>
                I want to work on real-world scenarios to solidify my understanding of how the 'interwebs' really work, from the basics of routing to setting up simple web servers.
            </p>
        </section>
    );

    // Component for the "Services Offered" and "Pricing" Section
    const ServicesSection = () => (
        <section className={`md:col-span-3 ${BG_DARK_CARD} p-8 rounded-xl shadow-2xl border-t-4 border-cyan-400`}>
            <h2 className={`text-3xl font-bold ${PRIMARY_ACCENT} mb-6 text-center flex items-center justify-center`}>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="w-7 h-7 mr-3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                How I Can Help You (Simplifying the Internet)
            </h2>

            <p className={`text-center text-lg ${TEXT_SUBTLE} mb-8 max-w-2xl mx-auto`}>
                Do you feel lost with simple technology tasks? I specialize in helping individuals and small businesses who don't have a big budget or technical knowledge.
            </p>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div className="text-center p-4 bg-gray-900 rounded-lg shadow-inner shadow-gray-700/50 hover:bg-gray-700 transition duration-200 border border-gray-700">
                    <span className={`text-3xl ${SECONDARY_HIGHLIGHT} font-bold block mb-2`}>1. Simple Website Setup</span>
                    <p className={`text-sm ${TEXT_SUBTLE}`}>Get your first presence online with a basic, modern, and effective website.</p>
                </div>
                <div className="text-center p-4 bg-gray-900 rounded-lg shadow-inner shadow-gray-700/50 hover:bg-gray-700 transition duration-200 border border-gray-700">
                    <span className={`text-3xl ${SECONDARY_HIGHLIGHT} font-bold block mb-2`}>2. Branded Email</span>
                    <p className={`text-sm ${TEXT_SUBTLE}`}>Stop using Gmail! Get a professional email like <code className="bg-gray-700 p-1 rounded">you@yourname.com</code>.</p>
                </div>
                <div className="text-center p-4 bg-gray-900 rounded-lg shadow-inner shadow-gray-700/50 hover:bg-gray-700 transition duration-200 border border-gray-700">
                    <span className={`text-3xl ${SECONDARY_HIGHLIGHT} font-bold block mb-2`}>3. Small Internet Tasks</span>
                    <p className={`text-sm ${TEXT_SUBTLE}`}>Need DNS help? Domain setup? Basic hosting advice? I can guide you.</p>
                </div>
            </div>

            {/* Commitment & Pricing */}
            <div className={`mt-10 p-6 ${PRIMARY_ACCENT.replace('text', 'bg').replace('400', '950')} rounded-lg border border-blue-400/50`}>
                <h3 className={`text-xl font-bold ${PRIMARY_ACCENT} mb-3`}>My Commitment & Pricing</h3>
                <p className={`${TEXT_LIGHT} italic`}>
                    As I am currently unemployed and on this practical learning journey, my fees are intentionally small. You are helping me gain vital hands-on experience, and I am helping you get online affordably.
                </p>
                <p className={`mt-2 text-lg font-semibold text-red-400`}>
                    <span className={PRIMARY_ACCENT}>Motto:</span> Practical experience over profit. Let's work together!
                </p>
            </div>
        </section>
    );

    return (
        <div className={`min-h-screen font-sans antialiased flex flex-col ${BG_DARK_PRIMARY}`}>
            
            {/* Header / Hero Section */}
            <header className="bg-blue-900 text-white py-16 shadow-2xl shadow-blue-950/50">
                <div className="container mx-auto px-4 text-center">
                    <h1 className="text-4xl sm:text-6xl font-extrabold tracking-tight">
                        Olá! I'm Your Local Algarve Tech Helper.
                    </h1>
                    <p className={`mt-4 text-xl sm:text-2xl font-light ${SECONDARY_HIGHLIGHT}`}>
                        Practical, budget-friendly web and networking assistance.
                    </p>
                </div>
            </header>

            {/* Main Content Grid */}
            <main className="container mx-auto px-4 py-12 flex-grow">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <AboutCard />
                    <JourneyCard />
                    <ServicesSection />
                </div>

                {/* Call to Action - Updated: No shadow, changed color to Indigo */}
                <div className="mt-16 text-center">
                    <a href="mailto:your.email@example.com" className={`inline-flex items-center px-10 py-5 bg-indigo-500 text-gray-900 text-xl font-extrabold rounded-full hover:bg-indigo-400 transition duration-300 transform hover:scale-105`}>
                        <Mail className="w-6 h-6 mr-3 text-gray-900" />
                        Get in Touch & Start Your Project
                    </a>
                </div>
            </main>

            {/* Footer */}
            <footer className="bg-gray-950 text-white py-6 mt-16 border-t border-gray-800">
                <div className="container mx-auto px-4 text-center text-sm">
                    <p className={TEXT_SUBTLE}>&copy; 2025 Algarve Tech Helper. Based in the Algarve, focused on learning and growing.</p>
                </div>
            </footer>

        </div>
    );
};

export default App;
