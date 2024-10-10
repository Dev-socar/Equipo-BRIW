/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./views/**/*.{html,js,php}"],
  theme: {
    container: {
      center: true,
    },
    extend: {
    },
    colors: {
      cafe: "#674f23",
    },
    fontFamily: {
      poppins: ["Poppins-Regular"],
      poppinsBold: ["Poppins-Bold"],
    },
  },
  plugins: [],
}