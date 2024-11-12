/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./views/**/*.{html,js,php}"],
  theme: {
    container: {
      center: true,
    },
    extend: {
    },
    fontFamily: {
      poppins: ["Poppins-Regular"],
      poppinsBold: ["Poppins-Bold"],
    },
  },
  plugins: [],
}