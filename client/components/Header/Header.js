import React, { useState } from "react";
import s from './Header.module.scss';
import { MainLogo } from '../../public/logos/Logos';
import { useRouter } from "router";
import Link from "../../polyfills/link";
import { AiFillCaretDown, AiOutlineMenu } from "react-icons/ai";
import { BiUser } from "react-icons/bi";
import Dropdown from "../Dropdown/Dropdown";
import classNames from "classnames";
import { useWindowSize } from "../../scripts/hooks/useWindowSize";
import { CSSTransition } from 'react-transition-group';
import VerticalMenu from '../VerticalMenu/VerticalMenu';
import { removeJWT, removeRefreshToken } from "../../scripts/jwtService";

export default function Header({navConfig, username}) {
    const [openMobileNav, setOpenMobileNav] = useState(false);
    const router = useRouter();
    const size = useWindowSize();

    function logout() {
        removeJWT();
        removeRefreshToken();
        router.push('/');
    }

    return (
        <div className={s.content}>
            <div className={s.container}>
                <div className={s.brand}>
                    <div className={s.logo}>
                        <MainLogo/>
                    </div>
                    <span className={s.name}>EduGameTheory</span>
                </div>

                {
                    size !== 'xs'
                    ?
                    <>
                        <div className={s.nav}>
                            {
                                navConfig && navConfig.map(({name, submenus, href}, i) =>
                                    submenus
                                    ?
                                    <Dropdown 
                                        className={classNames(s.element, s.element_multi, {[s.active] : router.pathname.includes(href)})}
                                        key={`header-${i}`}
                                        value={
                                        <span className={s.title}>{name}
                                            <AiFillCaretDown className={s.element_dropdown_icon}/>
                                        </span>
                                        }
                                    >
                                        {
                                            submenus.map(({name, href}, j) => 
                                                <Link href={href} key={`dropdown-${j}`}>
                                                    <span className={classNames(s.dropdown_item, {[s.active] : router.pathname.includes(href)})} >{name}</span>
                                                </Link>
                                            )
                                        }
                                    </Dropdown>
                                    :
                                    <Link href={href} key={`header-${i}`}>
                                        <div className={classNames(s.element, {[s.active] : router.pathname.includes(href)})}>
                                            <span className={s.title}>{name}</span>
                                        </div>
                                    </Link>
                                )
                            }
                        </div>
                        
                        <Dropdown
                            className={s.profile_container}
                            value={<div className={s.profile}><BiUser className={s.icon}/></div>}
                            float={'right'}>
                            <div className={s.dropdown_item_nonselect}>
                                <span className={s.title}>Вы авторизованы как <strong>{username}</strong></span>
                            </div>
                            <Link href={'/profile'}>
                                <div className={classNames(s.dropdown_item, {[s.active] : router.pathname.includes('/profile')})}>
                                    <span className={s.title}>Личный кабинет</span>
                                </div>
                            </Link>
                            <div className={s.dropdown_item} onClick={() => logout()}>
                                <span className={s.title}>Выход</span>
                            </div>
                        </Dropdown>
                    </>
                    :
                    <>
                        <div className={s.hamburger} onClick={() => setOpenMobileNav(state => !state)}><AiOutlineMenu className={s.icon}/></div>
                        <CSSTransition in={openMobileNav} timeout={300} 
                            classNames={{
                                enter: s.mobile_nav_enter,
                                enterDone: s.mobile_nav_enter_done
                            }}>
                            <VerticalMenu className={s.mobile_nav}>
                                {
                                    navConfig && navConfig.map(({name, submenus, href}, i) =>
                                        submenus 
                                        ?
                                        <>
                                            <div className={classNames(s.mobile_nav_element, {[s.active] : router.pathname.includes(href)})}>
                                                <span className={s.title}>
                                                    {name}
                                                    <AiFillCaretDown className={s.mobile_nav_element_submenu_icon}/>
                                                </span>
                                            </div>
                                            <div className={s.mobile_nav_element_submenu}>
                                                {
                                                    submenus.map(({name, href}, i) => 
                                                        <Link href={href} key={i}>
                                                            <div className={classNames(s.mobile_nav_element, {[s.active] : router.pathname.includes(href)})}>
                                                                <span className={s.title}>{name}</span>
                                                            </div>
                                                        </Link>
                                                    )
                                                }
                                            </div>
                                        </>
                                        :
                                        <Link href={href} key={i}>
                                            <div className={classNames(s.mobile_nav_element, {[s.active] : router.pathname.includes(href)})}>
                                                <span className={s.title}>{name}</span>
                                            </div>
                                        </Link>
                                    )
                                }
                                <hr></hr>
                                <Link href={'/profile'}>
                                    <div className={classNames(s.mobile_nav_element, {[s.active] : router.pathname.includes('/profile')})}>
                                        <span className={s.title}>Настройки</span>
                                    </div>
                                </Link>
                                <div className={s.mobile_nav_element} onClick={() => logout()}>
                                    <span className={s.title}>Выход</span>
                                </div>
                            </VerticalMenu>
                        </CSSTransition>
                    </>
                }
            </div>
        </div>
    );
}